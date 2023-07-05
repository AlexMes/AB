<?php

namespace App\Reports\AccountsBanned;

use App\Facebook\Account;
use App\Group;
use App\Insights;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\AccountsBanned
 */
class Report implements Responsable, Arrayable
{
    public const SPEND_RANGES = [
        '0'             => '0',
        '0.01 - 25'     => '0.01-25',
        '25 - 50'       => '25-50',
        '50 - 100'      => '50-100',
        '100 - 200'     => '100-200',
        '200 - 500'     => '200-500',
        '500 - 1000'    => '500-1000',
        '1000 - 2000'   => '1000-2000',
        'больше 2000'   => '2000-10000000',
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
     * Offers filter
     *
     * @var array
     */
    protected $offers;

    /**
     * Groups filter
     *
     * @var array|null
     */
    protected $groups;

    /**
     * Determines how to group statistics
     *
     * @var string|null
     */
    protected $groupBy;

    /**
     * UTM Campaign filter
     *
     * @var array
     */
    protected $utm_campaign;

    /**
     * Determines when we should filter strictly
     *
     * @var bool
     */
    protected $isStrict = false;

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
     * @return \App\Reports\AccountsBanned\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'users'        => $request->get('users'),
            'offers'       => $request->get('offers'),
            'groups'       => $request->get('groups'),
            'groupBy'      => $request->get('groupBy'),
            'utmCampaign'  => $request->get('campaign'),
            'teams'        => $request->get('teams'),
        ]);
    }

    /**
     * Report constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forTeams($settings['teams'])
            ->forGroups($settings['groups'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forAccounts($settings['accounts'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->groupBy($settings['groupBy'] ?? null)
            ->forCampaign($settings['utmCampaign'] ?? null)
            ->guessStrict();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\AccountsBanned\Report
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
     * @return \App\Reports\AccountsBanned\Report
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

        return [
            'headers'  => Headers::build($this->groupBy),
            'rows'     => Rows::build($insights, $this->accounts, $this->groupBy, $this->isStrict)->toArray(),
            'summary'  => Summary::build($insights, $this->accounts, $this->groupBy, $this->isStrict)->toArray(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
            'rk'       => $this->accounts->count(),
        ];
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Reports\AccountsBanned\Report
     */
    public function forAccounts($accounts = null)
    {
        $this->accounts = Account::visible()
            ->banned()
            ->whereBetween('banned_at', [
                $this->since->copy()->startOfDay()->toDateTimeString(),
                $this->until->copy()->endOfDay()->toDateTimeString()
            ])
            ->notEmptyWhereIn('group_id', $this->groups)
            ->when($this->users, function ($query) {
                $query->whereIn('id', Account::forUsers($this->users)->pluck('id')->toArray());
            })
            ->notEmptyWhereIn('id', $accounts)
            ->when($this->teams, function ($query) {
                return $query->whereHas('profile', function ($q) {
                    return $q->join('team_user', 'facebook_profiles.user_id', '=', 'team_user.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->with(['user' => function ($query) {
                $query->select(['users.id', 'users.name']);
            }])
            ->addSelect([
                'group_name' => Group::select('name')
                    ->whereColumn('id', 'facebook_ads_accounts.group_id')
                    ->limit(1)
            ])
            ->get([
                'user',
                'group_id',
                'group_name',
                'account_id',
                'banned_at',
                'created_at',
                'name',
            ]);

        return $this;
    }

    /**
     * Filter report by users
     *
     * @param array|string $users
     *
     * @return \App\Reports\AccountsBanned\Report
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
     * Filter by offers
     *
     * @param array $offers
     *
     * @return \App\Reports\AccountsBanned\Report
     */
    protected function forOffers($offers)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Load insights
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function insights()
    {
        return Insights::allowedOffers()->visible()
            ->whereIn('account_id', $this->accounts->pluck('account_id')->unique()->toArray())
            ->notEmptyWhereIn('facebook_cached_insights.user_id', $this->users)
            ->when($this->utm_campaign, function ($query) {
                $query->whereHas('campaign', function ($query) {
                    $query->where('name', 'like', "%campaign-{$this->utm_campaign}");
                });
            })
            ->when($this->offers, function ($query) {
                $query->whereIn('offer_id', $this->offers);
            }, function ($query) {
                $query->whereNotNull('offer_id');
            })
            ->when($this->teams, function ($query) {
                return $query->join('team_user', 'facebook_cached_insights.user_id', '=', 'team_user.user_id')
                    ->whereIn('team_user.team_id', $this->teams);
            })
            ->select([
                DB::raw("sum(spend::decimal) as spend"),
                'account_id',
            ])
            ->groupBy('account_id')
            ->get();
    }

    /**
     * Filter report by groups
     *
     * @param array|null $groups
     *
     * @return \App\Reports\AccountsBanned\Report
     */
    protected function forGroups(array $groups = null)
    {
        $this->groups = $groups;

        return  $this;
    }

    /**
     * @param string $groupBy
     *
     * @return Report
     */
    protected function groupBy($groupBy = 'account')
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param string $campaign
     *
     * @return \App\Reports\AccountsBanned\Report
     */
    protected function forCampaign($campaign)
    {
        $this->utm_campaign = $campaign;

        return $this;
    }

    /**
     * Determine when we should filter strictly
     *
     * @return \App\Reports\AccountsBanned\Report
     */
    protected function guessStrict()
    {
        if (
            ! empty($this->utm_campaign) ||
            ! empty($this->offers)
        ) {
            $this->isStrict = true;
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
