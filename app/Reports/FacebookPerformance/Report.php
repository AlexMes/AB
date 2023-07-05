<?php

namespace App\Reports\FacebookPerformance;

use App\Domain;
use App\Facebook\Account;
use App\Facebook\Ad;
use App\Queries\PerformanceDeposits;
use App\Queries\PerformanceInsights;
use App\Queries\PerformanceTraffic;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Class Report
 *
 * @package App\Reports\FacebookPerformance
 */
class Report implements Responsable, Arrayable
{
    public const LEVEL_ACCOUNT  = 'account';
    public const LEVEL_CAMPAIGN = 'campaign';
    public const LEVEL_ADSET    = 'adset';
    public const LEVEL_AD       = 'ad';
    public const LEVELS         = [
        self::LEVEL_ACCOUNT,
        self::LEVEL_CAMPAIGN,
        self::LEVEL_ADSET,
        self::LEVEL_AD,
    ];

    public const PART_FB    = 'fb';
    public const PART_AN    = 'an';
    public const PART_ALL   = 'all';
    public const PART_LP    = 'lp';
    public const PART_PL    = 'pl';
    public const PARTS      = [
        self::PART_FB,
        self::PART_AN,
        self::PART_ALL,
        self::PART_LP,
        self::PART_PL,
    ];

    /**
     * Start date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * End date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * Accounts used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $accounts;

    /**
     * Users collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $users;

    /**
     * Collection of results row
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Level of reporting
     *
     * @var string|null
     */
    protected $level;

    /**
     * Offers filter
     *
     * @var array
     */
    protected $offers;

    /**
     * UTM Campaign filter
     *
     * @var array
     */
    protected $utm_campaign;

    /**
     * UTM Campaign filter
     *
     * @var string
     */
    protected $utm_content;

    /**
     * Determines when we have to cut FB columns
     *
     * @var bool
     */
    protected bool $hideFacebook;

    /**
     * Groups filter
     *
     * @var array|null
     */
    protected $groups;

    /**
     * Determines accounts are selected or not
     *
     * @var bool
     */
    protected $hasAccounts = false;

    /**
     * Tags filter
     *
     * @var array|null
     */
    protected $tags;

    /**
     * @var string|null
     */
    protected ?string $part;
    protected $ads;

    /**
     * Teams filter
     *
     * @var array
     */
    protected $teams;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'level'         => $request->get('level'),
            'users'         => $request->get('users'),
            'accounts'      => $request->get('accounts'),
            'utmCampaign'   => $request->get('campaign'),
            'utmContent'    => $request->get('content'),
            'hideFacebook'  => $request->boolean('hideFacebook'),
            'groups'        => $request->get('groups'),
            'tags'          => $request->get('tags'),
            'part'          => $request->get('part'),
            'orders'        => $request->get('orders'),
            'teams'         => $request->get('teams'),
        ]);
    }

    /**
     * DailyReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->atLevel($settings['level'] ?? self::LEVEL_ACCOUNT)
            ->forGroups($settings['groups'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forAccounts($settings['accounts'] ?? null)
            ->forContent($settings['utmContent'] ?? null)
            ->forTags($settings['tags'] ?? null)
            ->hideFacebook($settings['hideFacebook'] ?? false)
            ->forPart($settings['part'] ?? null)
            ->forOrder($settings['orders'] ?? null)
            ->forTeams($settings['teams'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function since($since = null)
    {
        if (is_null($since)) {
            $this->since = now();

            return $this;
        }

        $this->since = Carbon::parse($since);

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * Get response representation of the report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        $insights = $this->insights();
        $deposits = $this->deposits();
        $traffic  = $this->traffic();

        $hideFb = $this->hideFacebook && !in_array($this->level, [Report::LEVEL_AD, Report::LEVEL_ADSET]);

        return [
            'headers'  => $hideFb
                ? array_diff(Headers::forLevel($this->level), Headers::fbKeys())
                : Headers::forLevel($this->level),
            'rows'     => Rows::build($insights, $deposits, $this->level, $traffic, $hideFb)->toArray(),
            'summary'  => Summary::build($insights, $deposits, $this->level, $traffic, $hideFb)->toArray(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function forAccounts($accounts = null)
    {
        if ($accounts === null) {
            $this->accounts = $this->users === null
                ? Account::visible()->notEmptyWhereIn('group_id', $this->groups)->get(['account_id','name'])
                : Account::forUsers($this->users)->when($this->groups, function ($accounts) {
                    return $accounts->whereIn('group_id', $this->groups);
                });

            $this->accounts->push(['account_id' => '','name' => 'No account']);

            return $this;
        }

        $this->hasAccounts = true;

        $this->accounts = Account::visible()
            ->notEmptyWhereIn('group_id', $this->groups)
            ->whereIn('id', Arr::wrap($accounts))->get(['account_id','name']);
        $this->accounts->push(['account_id' => '','name' => 'No account']);

        return $this;
    }

    /**
     * Filter report by users
     *
     * @param array|string $users
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function forUsers($users)
    {
        if ($users !== null) {
            $this->users = User::whereIn('id', Arr::wrap($users))->pluck('id')->toArray();

            return $this;
        }
        $this->users = null;

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $level
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function atLevel($level)
    {
        $this->level = in_array($level, self::LEVELS) ? $level : self::LEVEL_ACCOUNT;

        return $this;
    }

    /**
     * Filter by UTM content
     *
     * @param string $content
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    protected function forContent($content)
    {
        $this->utm_content = $content;

        return $this;
    }

    /**
     * Load insights
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function insights()
    {
        return PerformanceInsights::fetch()
            ->forPeriod($this->since, $this->until)
            ->forAccounts($this->accounts->pluck('account_id')->unique())
            ->forContent($this->utm_content)
            ->forTags($this->tags)
            ->atLevel($this->level)
            ->forPart($this->part)
            ->forUsers($this->users)
            ->forAds($this->ads)
            ->forTeams($this->teams)
            ->get();
    }

    /**
     * Fetch deposits
     *
     * @return \Illuminate\Support\Collection
     */
    protected function deposits()
    {
        return PerformanceDeposits::fetch()
            ->strict($this->hasAccounts || ! empty($this->groups))
            ->forPeriod($this->since, $this->until)
            ->forAccounts($this->accounts->pluck('account_id')->push(null)->unique())
            ->forContent($this->utm_content)
            ->forUsers($this->users)
            ->forTags($this->tags)
            ->atLevel($this->level)
            ->forPart($this->part)
            ->forAds($this->ads)
            ->forTeams($this->teams)
            ->get();
    }

    /**
     * Cached binom's statistic
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function traffic()
    {
        return PerformanceTraffic::fetch()
            ->strict($this->hasAccounts || ! empty($this->groups))
            ->forPeriod($this->since, $this->until)
            ->forAccounts($this->accounts->pluck('account_id')->push(null)->unique())
            ->forContent($this->utm_content)
            ->forUsers($this->users)
            ->forTags($this->tags)
            ->forPart($this->part)
            ->forAds($this->ads)
            ->forTeams($this->teams)
            ->get();
    }

    /**
     * Set visibility for Facebook data
     *
     * @param bool $hideFacebook
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    protected function hideFacebook(bool $hideFacebook)
    {
        $this->hideFacebook = ! auth()->user()->showFbFields;

        return  $this;
    }

    /**
     * Filter report by groups
     *
     * @param array|null $groups
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    protected function forGroups(array $groups = null)
    {
        $this->groups = $groups;

        return  $this;
    }

    /**
     * Filter report by tags
     *
     * @param array|null $tags
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    protected function forTags(array $tags = null)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $part
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function forPart($part)
    {
        $this->part = in_array($part, self::PARTS) ? $part : null;

        return $this;
    }

    /**
     * Limit to order
     *
     * @param $order
     *
     * @return $this
     */
    public function forOrder($order = null)
    {
        if ($order !== null) {
            $this->ads = Ad::query()
                ->whereIn(
                    'domain_id',
                    Domain::whereIn('order_id', $order)->pluck('id')->toArray()
                )->pluck('id')->toArray();
        }

        return $this;
    }

    /**
     * Filter by team
     *
     * @param null|array $teams
     *
     * @return $this
     */
    public function forTeams($teams = null)
    {
        $this->teams = $teams;

        return $this;
    }
}
