<?php

namespace App\Queries;

use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Insights;
use App\Reports\Performance\Report;

class PerformanceInsights
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
    protected $insights;

    /**
     * Offers filter
     *
     * @var array|null
     */
    protected $offers;

    /**
     * Level of query
     *
     * @var string
     */
    protected $level;

    /**
     * Buyers filter
     *
     * @var array|null
     */
    protected ?array $users;

    /**
     * Array of fields required for query
     *
     * @var array
     */
    protected static $fields = [
        'date','account_id','campaign_id',
        'ad_id','adset_id','offer_id','reach',
        'impressions','spend','leads_cnt', 'link_clicks'
    ];

    /**
     * Performance Insights Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->insights = Insights::allowedOffers()->visible();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forPeriod($since, $until)
    {
        $this->insights->whereBetween('date', [$since->toDateString(), $until->toDateString()]);

        return $this;
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forAccounts($accounts = null)
    {
        if ($accounts === null) {
            return $this;
        }

        $this->insights->whereIn('account_id', $accounts);

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $level
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function atLevel($level = Report::LEVEL_CAMPAIGN)
    {
        if ($level === Report::LEVEL_ACCOUNT) {
            $this->insights->addSelect([
                'name' => Account::select('name')
                    ->whereColumn('account_id', 'facebook_cached_insights.account_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_CAMPAIGN) {
            $this->insights->addSelect([
                'name' => Campaign::select('utm_key')
                    ->whereColumn('id', 'facebook_cached_insights.campaign_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_ADSET) {
            $this->insights->addSelect([
                'name' => AdSet::select('name')
                    ->whereColumn('id', 'facebook_cached_insights.adset_id')
                    ->limit(1)
            ]);
        }
        if ($level === Report::LEVEL_AD) {
            $this->insights->addSelect([
                'name' => Ad::select('name')
                    ->whereColumn('id', 'facebook_cached_insights.ad_id')
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
     * @return \App\Queries\PerformanceInsights
     */
    public function forOffers($offers = null)
    {
        if ($offers === null) {
            $this->insights->whereNotNull('offer_id');

            return $this;
        }

        $this->insights->whereIn('offer_id', $offers);

        return $this;
    }

    /**
     * Filter report for specific campaign
     *
     * @param string $campaign
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forCampaign($campaign = null)
    {
        if ($campaign === null) {
            return $this;
        }

        $this->insights->whereHas('campaign', function ($query) use ($campaign) {
            $query->where('name', 'like', "%campaign-{$campaign}");
        });

        return $this;
    }

    /**
     * Filter report for specific UTM content
     *
     * @param string $content
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forContent($content = null)
    {
        if ($content !== null) {
            $this->insights->whereHas('ad', function ($query) use ($content) {
                $query->where('name', '=', "{$content}");
            });
        }

        return $this;
    }

    public function forTags($tags = null)
    {
        if ($tags !== null) {
            $adIds = Ad::query()->whereHas('tags', function ($builder) use ($tags) {
                /** @var \Illuminate\Database\Eloquent\Builder $builder */
                $builder->whereIn('tag_id', $tags);
            })->pluck('id');

            $this->insights->whereIn('ad_id', $adIds);
        }

        return $this;
    }

    /**
     * Filter report for specific campaign part
     *
     * @param string|null $part
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forPart($part = null)
    {
        if ($part === null) {
            return $this;
        }

        if ($part === Report::PART_FB) {
            $this->insights->whereHas('campaign', function ($query) {
                return $query->where('utm_key', 'not like', '%-'    . Report::PART_AN . '%')
                    ->where('utm_key', 'not like', '%-'    . Report::PART_ALL . '%')
                    ->where('utm_key', '!=', Report::PART_AN)
                    ->orWhere('utm_key', 'like', '%-android');
            });
        }

        if ($part === Report::PART_AN) {
            $this->insights->whereHas('campaign', function ($query) use ($part) {
                return $query->where('utm_key', 'like', "%-{$part}%")
                    ->where('utm_key', 'not like', '%-android%')
                    ->orWhere('utm_key', '=', $part);
            });
        }

        if ($part === Report::PART_ALL) {
            $this->insights->whereHas('campaign', function ($query) use ($part) {
                return $query->where('utm_key', 'like', "%-{$part}%")
                    ->orWhere('utm_key', '=', $part);
            });
        }

        if ($part === Report::PART_LP) {
            $this->insights->whereHas('campaign', function ($query) use ($part) {
                return $query->where('utm_key', 'like', "%-{$part}%")
                    ->orWhere('utm_key', '=', $part);
            });
        }

        if ($part === Report::PART_PL) {
            $this->insights->whereHas('campaign', function ($query) use ($part) {
                return $query->where('utm_key', 'like', "%-{$part}%")
                    ->orWhere('utm_key', '=', $part);
            });
        }

        return $this;
    }

    /**
     * Filter report for specific users
     *
     * @param null|array $users
     *
     * @return \App\Queries\PerformanceInsights
     */
    public function forUsers($users = null)
    {
        if ($users === null) {
            return $this;
        }

        $this->insights->whereIn('user_id', $users);

        return $this;
    }

    /**
     * Perform query and return results
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->insights->get(self::$fields);
    }


    /**
     * Named constructor
     *
     * @return \App\Queries\PerformanceInsights
     */
    public static function fetch()
    {
        return new self();
    }

    public function forAds($ads = null)
    {
        if ($ads !== null) {
            $this->insights->whereIn('ad_id', $ads);
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
            $this->insights->join('team_user', 'facebook_cached_insights.user_id', '=', 'team_user.user_id')
                ->whereIn('team_user.team_id', $teams);
        }

        return $this;
    }
}
