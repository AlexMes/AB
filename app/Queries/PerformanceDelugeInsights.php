<?php

namespace App\Queries;

use App\ManualAccount;
use App\ManualCampaign;
use App\ManualInsight;
use App\Reports\Performance\Report;
use Illuminate\Database\Query\Builder;

class PerformanceDelugeInsights
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
        'leads_cnt',
    ];

    /**
     * Performance Insights Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->insights = ManualInsight::visible()->allowedOffers();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function atLevel($level = Report::LEVEL_CAMPAIGN)
    {
        if ($level === Report::LEVEL_ACCOUNT) {
            $this->insights->addSelect([
                'name' => ManualAccount::select('name')
                    ->whereColumn('manual_accounts.account_id', 'manual_insights.account_id')
                    ->limit(1)
            ]);
        }

        if ($level === Report::LEVEL_CAMPAIGN) {
            $this->insights->addSelect([
                'name' => ManualCampaign::select('utm_key')
                    ->whereColumn('manual_campaigns.id', 'manual_insights.campaign_id')
                    ->limit(1)
            ]);
        }

        /*if ($level === Report::LEVEL_ADSET) {
            $this->insights->addSelect([
                'name' => AdSet::select('name')
                    ->whereColumn('id', 'facebook_cached_insights.adset_id')
                    ->limit(1)
            ]);
        }*/

        if ($level === Report::LEVEL_AD) {
            $this->insights->addSelect([
                'name' => ManualCampaign::select('creo')
                    ->whereColumn('manual_campaigns.id', 'manual_insights.campaign_id')
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
     * @return $this
     */
    public function forOffers($offers = null)
    {
        if (!empty($offers)) {
            $this->insights->whereHas('campaign', function ($query) use ($offers) {
                return $query->whereHas('bundle', fn ($q) => $q->whereIn('offer_id', $offers));
            });
        }

        return $this;
    }

    /**
     * Filter report for specific campaign
     *
     * @param string $campaign
     *
     * @return $this
     */
    public function forCampaign($campaign = null)
    {
        if ($campaign === null) {
            return $this;
        }

        $this->insights->whereHas('campaign', function ($query) use ($campaign) {
            $query->where('utm_key', $campaign);
        });

        return $this;
    }

    /**
     * Filter report for specific UTM content
     *
     * @param string $content
     *
     * @return $this
     */
    public function forContent($content = null)
    {
        if ($content !== null) {
            $this->insights->whereHas('campaign', function ($query) use ($content) {
                $query->where('creo', $content);
            });
        }

        return $this;
    }

    public function forTags($tags = null)
    {
        // TODO skip for now
        /*if ($tags !== null) {
            $adIds = Ad::query()->whereHas('tags', function ($builder) use ($tags) {
                $builder->whereIn('tag_id', $tags);
            })->pluck('id');

            $this->insights->whereIn('ad_id', $adIds);
        }*/

        return $this;
    }

    /**
     * Filter report for specific campaign part
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
     * @return $this
     */
    public function forUsers($users = null)
    {
        if (!empty($users)) {
            $this->insights->whereHas('account', function ($query) use ($users) {
                return $query->whereIn('user_id', $users);
            });
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
        return $this->insights->get(self::$fields);
    }


    /**
     * Named constructor
     *
     * @return $this
     */
    public static function fetch()
    {
        return new self();
    }

    public function forAds($ads = null)
    {
        if ($ads !== null) {
            $this->insights->whereHas('campaign', function ($insightQuery) use ($ads) {
                return $insightQuery->whereExists(function (Builder $builder) use ($ads) {
                    return $builder->selectRaw('1')
                        ->from('facebook_ads')
                        ->whereColumn('facebook_ads.name', 'manual_campaigns.creo')
                        ->whereIn('facebook_ads.id', $ads);
                });
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
            $this->insights->whereHas('account', function ($accountQuery) use ($teams) {
                return $accountQuery->whereExists(function ($query) use ($teams) {
                    return $query->selectRaw('1')
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'manual_accounts.user_id')
                        ->whereIn('team_user.team_id', $teams);
                });
            });
        }

        return $this;
    }
}
