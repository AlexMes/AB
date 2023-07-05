<?php

namespace App\Facebook\Collectors;

use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Facebook\Contracts\Insightful;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\CampaignFields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InsightCollector implements Responsable, Arrayable
{

    /**
     * Collection of formatted insights
     *
     * @var \Illuminate\Support\Collection
     */
    protected $insights;

    /**
     * Start of time range
     *
     * @var string
     */
    protected $since;

    /**
     * Collection of AdAccounts
     *
     *s @var \Illuminate\Support\Collection
     */
    protected $accounts;

    /**
     * Level of Insights reporting.
     *
     * @var string
     */
    protected $level;

    /**
     * End of time range
     *
     * @var string
     */
    protected $until;

    /**
     * @var string
     */
    protected $status;

    /**
     * Collection of Facebook profiles
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $buyers;

    /**
     * InsightCollector constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->insights = collect();
    }

    /**
     * Set ad accounts
     *
     * @param mixed $accounts
     *
     * @return \App\Facebook\Collectors\InsightCollector |\Illuminate\Database\Eloquent\Collection
     */
    public function forAccounts($accounts)
    {
        if ($accounts !== null) {
            // Filtering by ads account
            $this->accounts = Account::visible()->whereIn('id', Arr::wrap($accounts))->distinct()->get();

            return $this;
        }

        $this->accounts = Account::visible()->get();

        return $this;
    }

    /**
     * Configure time range period
     *
     * @param array $period
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function forPeriod($period)
    {
        $this->since($period['since'])->until($period['until']);

        return $this;
    }


    /**
     * Set period since filter
     *
     * @param string $since
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function since($since)
    {
        $this->since = Carbon::parse($since)->toDateString();

        return $this;
    }

    /**
     * Set until filter
     *
     * @param string $until
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function until($until)
    {
        $this->until = Carbon::parse($until)->toDateString();

        return $this;
    }

    /**
     * Get formatted period for API request
     *
     * @return array
     */
    protected function getPeriod()
    {
        return ['since' => $this->since, 'until' => $this->until];
    }

    /**
     * Get array of filters
     *
     * @return array
     */
    protected function getFiltering()
    {
        return $this->status === null ? [] : [
            ['field' => "effective_status", 'operator' => 'IN', 'value' => $this->status]
        ];
    }
    /**
     * Set Facebook reporting level
     *
     * @param string $level
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function atLevel($level)
    {
        if ($level === null) {
            $level = Insightful::MODE_ACCOUNT;
        }
        $this->level = $level;

        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $status
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function forStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Setup social account
     *
     * @param $buyer
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function forUser($buyer)
    {
        // No buyers filtering
        if ($buyer === null) {
            $this->buyers = null;

            return $this;
        }

        $this->buyers = Arr::wrap($buyer);

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        try {
            return $this->collect();
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());

            return null;
        }
    }


    /**
     * Collect data from AdAccounts
     *
     * @return array
     */
    public function collect()
    {
        return $this->accounts->map(function (Account $account) {
            return $this->fetchInsightsForAccount($account);
        })->unless($this->level === Insightful::MODE_ACCOUNT, function (Collection $collection) {
            return $collection->flatten(1);
        })->reject(function ($item) {
            return $item === null;
        })->toArray();
    }

    /**
     * Collect ad account insights
     *
     * @param \App\Facebook\Account $account
     *
     * @return array
     */
    protected function fetchInsightsForAccount(Account $account)
    {
        return $this->{Str::plural($this->level)}($account);
    }

    /**
     * Collect accounts with insights
     *
     * @param \App\Facebook\Account $account
     *
     * @return array
     */
    private function accounts(Account $account)
    {
        return $this->toReportRow($account->toArray(), $this->getInsights($account->instance()));
    }

    /**
     * Collect campaigns with insights
     *
     * @param \App\Facebook\Account $account
     *
     * @return \Illuminate\Support\Collection
     */
    private function campaigns(Account $account)
    {
        return collect($account->instance()->getCampaigns(array_merge(Campaign::FB_FIELDS, [
            CampaignFields::BID_STRATEGY, CampaignFields::DAILY_BUDGET, CampaignFields::LIFETIME_BUDGET,
        ]), [
            'filtering'          => $this->getFiltering()
        ]))->map(function (\FacebookAds\Object\Campaign $campaign) {
            return $this->toReportRow($campaign->exportAllData(), $this->getInsights($campaign));
        });
    }

    /**
     * Collect adsets with insights
     *
     * @param \App\Facebook\Account $account
     *
     * @return \Illuminate\Support\Collection
     */
    private function adsets(Account $account)
    {
        return collect($account->instance()->getAdSets(array_merge(AdSet::FB_FIELDS, [
            AdSetFields::BID_STRATEGY, AdSetFields::DAILY_BUDGET, AdSetFields::LIFETIME_BUDGET, AdSetFields::BID_AMOUNT
        ]), [
            'filtering'          => $this->getFiltering()
        ]))->map(function (\FacebookAds\Object\Adset $adset) {
            return $this->toReportRow($adset->exportAllData(), $this->getInsights($adset));
        });
    }

    /**
     * Collect ads with insights
     *
     * @param \App\Facebook\Account $account
     *
     * @return \Illuminate\Support\Collection
     */
    private function ads(Account $account)
    {
        return collect($account->instance()->getAds(array_merge(Ad::FB_FIELDS, [
            AdFields::BID_TYPE, AdFields::BID_AMOUNT,
        ]), [
            'filtering'          => $this->getFiltering()
        ]))->map(function (\FacebookAds\Object\Ad $ad) {
            return $this->toReportRow($ad->exportAllData(), $this->getInsights($ad));
        });
    }

    /**
     * Load insights from the Facebook API
     *
     * @param  $object
     *
     * @return mixed
     */
    protected function getInsights($object)
    {
        return collect($object->getInsights(Insightful::FIELDS, [
            'time_range'         => $this->getPeriod(),
            'level'              => $this->level,
        ]))->map(function ($insight) {
            return $insight->exportAllData();
        })->first();
    }

    /**
     * Format data to insight row
     *
     * @param $object
     * @param $insight
     *
     * @return array
     */
    protected function toReportRow($object, $insight)
    {
        if (is_null($insight)) {
            return null;
        }

        return [
            'id'                   => $object['id'],
            'name'                 => $object['name'],
            'status'               => $object['effective_status'] ?? $object['status'],
            'bid_strategy'         => $object['bid_strategy'] ?? '-',
            'reach'                => $insight['reach'],
            'frequency'            => round($insight['frequency'], 2),
            'impressions'          => $insight['impressions'],
            'results'              => collect($insight['actions'] ?? [])->filter(function ($action) {
                return $action['action_type'] === 'lead';
            })->first()['value'] ?? 0,
            'budget'             => $this->budget($object),
            'budget_field'       => $this->budgetField($object),
            'spend'              => $insight['spend'],
            'cpr'                => round(collect($insight['cost_per_action_type'] ?? [])->filter(function ($action) {
                return $action['action_type'] === 'lead';
            })->first()['value'] ?? 0, 2),
            'cpm'                       => round($insight['cpm'], 2),
            'clicks'                    => $insight['clicks'],
            'cpc'                       => round($insight['cpc'] ?? 0, 2),
            'ctr'                       => round($insight['ctr'], 2),
            'link_clicks'               => round(collect($insight['actions'] ?? [])->filter(function ($action) {
                return $action['action_type'] === 'link_click';
            })->first()['value'] ?? 0, 2)
        ];
    }

    /**
     * Get formatted budget
     *
     * @param $object
     *
     * @return string
     */
    public function budget($object)
    {
        $value = $object['daily_budget'] ?? $object['lifetime_budget'] ?? 0;

        return  number_format($value / 100, 2, '.', ',');
    }

    /**
     * Gets budget filed(type)
     *
     * @param $object
     *
     * @return string|null
     */
    public function budgetField($object)
    {
        if (isset($object['daily_budget'])) {
            return 'daily_budget';
        } elseif (isset($object['lifetime_budget'])) {
            return 'lifetime_budget';
        }

        return null;
    }
}
