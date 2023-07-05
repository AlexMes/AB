<?php

namespace App\Queries;

use App\Deposit;
use App\Facebook\Account;
use App\Facebook\Ad;
use App\Lead;
use App\Reports\Performance\Report;
use App\TelegramChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PerformanceDeposits
{
    /**
     * Accounts to query
     *
     * @var \Illuminate\Support\Collection
     */
    protected $accounts;

    /**
     * Offers filter
     *
     * @var array|null
     */
    protected $offers;

    /**
     * Users filter
     *
     * @var array|null
     */
    protected $users;

    /**
     * Level of query
     *
     * @var string
     */
    protected $level;

    /**
     * Deposits query
     *
     * @var
     */
    protected $deposits;

    /**
     * @var bool
     */
    protected $isStrict = false;

    /**
     * Fields to fetch
     *
     * @var array
     */
    protected static $fields = [
        'id','lead_return_date','lead_id','account_id','user_id'
    ];

    /**
     * Performance Insights Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->deposits = Deposit::allowedOffers()->visible();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forPeriod($since, $until)
    {
        $this->deposits->whereBetween('lead_return_date', [$since->toDateString(), $until->toDateString()])
            ->whereDoesntHave('lead', fn ($query) => $query->whereNotNull('affiliate_id'));

        return $this;
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forAccounts($accounts = null)
    {
        $this->deposits->where(function (Builder $builder) use ($accounts) {
            $builder->whereIn('account_id', $accounts)
                ->when(! $this->isStrict, function ($query) {
                    $query->orWhere('account_id', null);
                });
        });

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $level
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function atLevel($level = Report::LEVEL_CAMPAIGN)
    {
        if ($level === Report::LEVEL_ACCOUNT) {
            $this->deposits->addSelect([
                'name' => Account::select('name')
                    ->whereColumn('account_id', 'deposits.account_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_CAMPAIGN) {
            $this->deposits->addSelect([
                'name' => Lead::select(DB::raw('lower(utm_campaign) as utm_campaign'))
                    ->whereColumn('id', 'deposits.lead_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_AD) {
            $this->deposits->addSelect([
                'name' => Lead::select('utm_content')
                    ->whereColumn('id', 'deposits.lead_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_ADSET) {
            $this->deposits->addSelect([
                'name' => Lead::select('utm_term')
                    ->whereColumn('id', 'deposits.lead_id')
                    ->limit(1)
            ]);
        }

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param array $offers
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forOffers($offers)
    {
        if ($offers !== null) {
            $this->deposits->whereIn('offer_id', $offers);
        }

        return $this;
    }

    /**
     * Filter deposits by user
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forUsers($users = null)
    {
        if ($users !== null) {
            $this->deposits->whereIn('user_id', $users);
        }

        return $this;
    }

    /**
     * Perform query and return results
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->deposits->get(self::$fields);
    }

    /**
     * Named constructor
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public static function fetch()
    {
        return new self();
    }

    /**
     * @param $utmCampaign
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forCampaign($utmCampaign = null)
    {
        if ($utmCampaign === null) {
            return $this;
        }
        $this->deposits->whereHas('lead', function ($query) use ($utmCampaign) {
            return $query->where('utm_campaign', $utmCampaign);
        });

        return $this;
    }

    /**
     * @param $utmContent
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forContent($utmContent = null)
    {
        if ($utmContent !== null) {
            $this->deposits->whereHas('lead', function ($query) use ($utmContent) {
                return $query->where('utm_content', $utmContent);
            });
        }

        return $this;
    }

    public function forTags($tags = null)
    {
        if ($tags !== null) {
            $accountIds = Ad::query()->whereHas('tags', function ($builder) use ($tags) {
                /** @var \Illuminate\Database\Eloquent\Builder $builder */
                $builder->whereIn('tag_id', $tags);
            })->pluck('account_id')->unique();

            $this->deposits->whereIn('account_id', $accountIds);
        }

        return $this;
    }

    /**
     * This strict mode need for account fetching when users filter is not chosen
     *
     * @param bool $isStrict
     *
     * @return $this
     */
    public function strict($isStrict = false)
    {
        $this->isStrict = $isStrict;

        return $this;
    }

    /**
     * Filter report for specific subject
     *
     * @param string $subject
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forSubject($subject = null)
    {
        if ($subject != null) {
            $channels = TelegramChannel::where('subject_id', $subject)->pluck('name');
            $this->deposits->whereHas('lead', function ($query) use ($channels) {
                return $query->whereIn('utm_campaign', $channels);
            });
        }

        return $this;
    }

    /**
     * @param string|null $part
     *
     * @return \App\Queries\PerformanceDeposits
     */
    public function forPart($part = null)
    {
        if ($part === null) {
            return $this;
        }

        if ($part === Report::PART_FB) {
            $this->deposits->whereHas('lead', function ($query) {
                return $query->where('utm_campaign', 'not like', '%-' . Report::PART_AN . '%')
                    ->where('utm_campaign', 'not like', '%-' . Report::PART_ALL . '%');
            });
        }

        if ($part === Report::PART_AN) {
            $this->deposits->whereHas('lead', fn ($query) => $query->where('utm_campaign', 'like', "%-{$part}%"));
        }

        if ($part === Report::PART_ALL) {
            $this->deposits->whereHas('lead', fn ($query) => $query->where('utm_campaign', 'like', "%-{$part}%"));
        }

        if ($part === Report::PART_LP) {
            $this->deposits->whereHas('lead', fn ($query) => $query->where('utm_campaign', 'like', "%-{$part}%"));
        }

        if ($part === Report::PART_PL) {
            $this->deposits->whereHas('lead', fn ($query) => $query->where('utm_campaign', 'like', "%-{$part}%"));
        }

        return $this;
    }

    public function forAds($ads = null)
    {
        if ($ads !== null) {
            $this->deposits->whereHas('lead', function ($query) use ($ads) {
                return $query->whereIn('campaign_id', Ad::whereIn('id', $ads)->pluck('campaign_id'));
            });
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
        if (!empty($teams)) {
            $this->deposits->join('team_user', 'deposits.user_id', '=', 'team_user.user_id')
                ->whereIn('team_user.team_id', $teams);
        }

        return $this;
    }
}
