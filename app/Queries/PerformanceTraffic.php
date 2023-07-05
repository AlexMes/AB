<?php

namespace App\Queries;

use App\Binom\Campaign;
use App\Binom\Statistic;
use App\Facebook\Account;
use App\Facebook\Ad;
use App\Reports\Performance\Report;
use Illuminate\Database\Eloquent\Builder;

class PerformanceTraffic
{
    /**
     * Accounts to query
     *
     * @var \Illuminate\Support\Collection
     */
    protected $accounts;

    /**
     * Insights query builder
     *
     * @var \App\Insights
     */
    protected $statistics;

    /**
     * Offers filter
     *
     * @var array|null
     */
    protected $offers;

    /**
     * @var bool
     */
    protected $isStrict = false;

    /**
     * Performance Insights Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->statistics = Statistic::allowedOffers();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \app\Queries\PerformanceTraffic
     */
    public function forPeriod($since, $until)
    {
        $this->statistics->whereBetween('date', [$since->toDateString(), $until->toDateString()])
            ->addSelect(['accountName' =>
                Account::select('name')
                    ->whereColumn('account_id', 'binom_statistics.account_id')
                    ->limit(1)
            ]);

        return $this;
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \app\Queries\PerformanceTraffic
     */
    public function forAccounts($accounts = null)
    {
        if ($accounts !== null) {
            $this->statistics->where(function (Builder $builder) use ($accounts) {
                $builder->whereIn('account_id', $accounts)
                    ->when(! $this->isStrict, function ($query) {
                        $query->orWhere('account_id', null);
                    });
            });
        }


        return $this;
    }

    /**
     * Filter by offers
     *
     * @param array $offers
     *
     * @return \app\Queries\PerformanceTraffic
     */
    public function forOffers($offers = null)
    {
        if ($offers === null) {
            $this->statistics->whereNotNull('campaign_id');

            return $this;
        }

        $this->statistics->whereIn(
            'campaign_id',
            Campaign::whereIn('offer_id', $offers)->pluck('id')
        );

        return $this;
    }

    /**
     * Perform query and return results
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->statistics->get();
    }


    /**
     * Named constructor
     *
     * @return \app\Queries\PerformanceTraffic
     */
    public static function fetch()
    {
        return new self();
    }

    /**
     * Filter for UTM campaign
     *
     * @param null $utmCampaign
     *
     * @return $this
     */
    public function forCampaign($utmCampaign = null)
    {
        if ($utmCampaign === null) {
            return $this;
        }

        $this->statistics->where('utm_campaign', $utmCampaign);

        return $this;
    }

    /**
     * Filter for UTM content
     *
     * @param null       $utmcontent
     * @param null|mixed $utmContent
     *
     * @return $this
     */
    public function forContent($utmContent = null)
    {
        if ($utmContent !== null) {
            $this->statistics->where('utm_content', $utmContent);
        }

        return $this;
    }

    /**
     * Filter by user
     *
     * @param null $users
     *
     * @return \App\Queries\PerformanceTraffic
     */
    public function forUsers($users = null)
    {
        if ($users !== null) {
            $this->statistics->whereIn('user_id', $users);
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

            $this->statistics->whereIn('account_id', $accountIds);
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
     * Filter for campaign part
     *
     * @param string|null $part
     *
     * @return $this
     */
    public function forPart($part = null)
    {
        if ($part === null) {
            return $this;
        }

        if ($part === Report::PART_FB) {
            $this->statistics->where('utm_campaign', 'not like', '%-' . Report::PART_AN . '%')
                ->where('utm_campaign', 'not like', '%-' . Report::PART_ALL . '%')
                ->where('utm_campaign', '!=', Report::PART_AN);
        }

        if ($part === Report::PART_AN) {
            $this->statistics->where(function ($query) use ($part) {
                $query->where('utm_campaign', 'like', "%-{$part}%")
                    ->where('utm_campaign', 'not like', '%-android')
                    ->orWhere('utm_campaign', '=', $part);
            });
        }

        if ($part === Report::PART_ALL) {
            $this->statistics->where(function ($query) use ($part) {
                $query->where('utm_campaign', 'like', "%-{$part}%")
                    ->orWhere('utm_campaign', '=', $part);
            });
        }

        if ($part === Report::PART_LP) {
            $this->statistics->where(function ($query) use ($part) {
                $query->where('utm_campaign', 'like', "%-{$part}%")
                    ->orWhere('utm_campaign', '=', $part);
            });
        }

        if ($part === Report::PART_PL) {
            $this->statistics->where(function ($query) use ($part) {
                $query->where('utm_campaign', 'like', "%-{$part}%")
                    ->orWhere('utm_campaign', '=', $part);
            });
        }

        return $this;
    }

    public function forAds($ads = null)
    {
        if ($ads !== null) {
            $this->statistics->whereIn('fb_ad_id', $ads);
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
            $this->statistics->join('team_user', 'binom_statistics.user_id', '=', 'team_user.user_id')
                ->whereIn('team_user.team_id', $teams);
        }

        return $this;
    }
}
