<?php

namespace App\Facebook\Jobs;

use App\AgeInsights;
use App\Facebook\Account;
use App\Facebook\Campaign;
use App\Facebook\Contracts\Insightful;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Http\Exception\PermissionException;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Object\Fields\AdFields;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CacheAgeInsights implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

    protected const FIELDS = [
        AdFields::ID,
        AdFields::NAME,
        AdFields::ACCOUNT_ID,
        AdFields::CAMPAIGN_ID,
        AdFields::ADSET_ID,
    ];

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * What campaign should be used to load insights
     *
     * @var \App\Facebook\Account
     */
    protected $account;

    /**
     * For what exactly date we should cache insights
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * CollectInsights constructor.
     *
     * @param \App\Facebook\Account      $account
     * @param \Illuminate\Support\Carbon $date
     *
     */
    public function __construct(Account $account, $date = null)
    {
        $this->account  = $account;
        $this->date     = Carbon::parse($date) ?? now();
    }

    /**
     * Handle the job
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->insights()->each(function ($insight) {
                $this->missing($insight) ? $this->create($insight) : $this->update($insight);
            });
        } catch (AuthorizationException | PermissionException $exception) {
            // Shit happens, stop bloating logs.
        }
    }

    /**
     * Retrieve insights from the Facebook API
     *
     * @return \Illuminate\Support\Collection
     */
    protected function retrieve()
    {
        return collect($this->account->instance()
            ->getAds(self::FIELDS))
            ->map(function (Ad $ad) {
                return $this->fetchInsights($ad)
                    ->map(function ($insight) use ($ad) {
                        return array_merge(
                            ['offer_id' => $this->getOffer($ad)],
                            $ad->exportAllData(),
                            $insight
                        );
                    });
            })
            ->flatten(1);
    }

    /**
     * Load insights from Facebook API
     *
     * @return \Illuminate\Support\Collection
     */
    protected function insights()
    {
        return optional($this->retrieve())
            ->reject(function ($insight) {
                return empty($insight);
            })
            ->map(function ($insight) {
                return [
                    'date'        => $this->date->toDateString(),
                    'account_id'  => $insight['account_id'],
                    'campaign_id' => $insight['campaign_id'],
                    'adset_id'    => $insight['adset_id'],
                    'ad_id'       => $insight['id'],
                    'offer_id'    => $insight['offer_id'],
                    'reach'       => $insight['reach'] ?? 0,
                    'impressions' => $insight['impressions'] ?? 0,
                    'spend'       => $insight['spend'] ?? 0,
                    'cpm'         => $insight['cpm'] ?? 0,
                    'cpc'         => $insight['cpc'] ?? 0,
                    'ctr'         => $insight['ctr'] ?? 0,
                    'clicks'      => $insight['clicks'] ?? 0,
                    'cpl'         => $this->cpl($insight) ?? 0,
                    'frequency'   => $insight['frequency'] ?? 0.00,
                    'leads_cnt'   => collect($insight['actions'] ?? [])->filter(function ($action) {
                        return $action['action_type'] === 'lead';
                    })->first()['value'] ?? 0,
                    'link_clicks' => collect($insight['actions'] ?? [])->filter(function ($action) {
                        return $action['action_type'] === 'link_click';
                    })->first()['value'] ?? 0,
                    'actions'     => $insight['actions'] ?? null,
                    'age'         => $insight['age'],
                ];
            });
    }

    /**
     * Calculate cpl for campaign
     *
     * @param $attributes
     *
     * @return float|int
     */
    protected function cpl($attributes)
    {
        $leads = collect($attributes['actions'] ?? [])->filter(function ($action) {
            return $action['action_type'] === 'lead';
        })->first();

        if ($leads) {
            return $attributes['spend'] / $leads['value'];
        }

        return 0;
    }

    /**
     * Update existing stats
     *
     * @param array $insight
     *
     * @return void
     */
    protected function update($insight)
    {
        AgeInsights::query()
            ->whereDate('date', $this->date->toDateString())
            ->where('account_id', $insight['account_id'])
            ->where('campaign_id', $insight['campaign_id'])
            ->where('adset_id', $insight['adset_id'])
            ->where('ad_id', $insight['ad_id'])
            ->where('age', $insight['age'])
            ->update(Arr::except($insight, ['account_id','campaign_id','ad_id','adset_id']));
    }

    /**
     * Save new stats
     *
     * @param array $insight
     *
     * @return void
     */
    protected function create($insight)
    {
        AgeInsights::create($insight);
    }

    /**
     * Determine is insight already cached
     *
     * @param array $insight
     *
     * @return bool
     */
    protected function missing($insight)
    {
        return AgeInsights::query()
            ->whereDate('date', $this->date->toDateString())
            ->where('account_id', $this->account->account_id)
            ->where('campaign_id', $insight['campaign_id'])
            ->where('adset_id', $insight['adset_id'])
            ->where('ad_id', $insight['ad_id'])
            ->where('age', $insight['age'])
            ->first() === null;
    }

    /**
     * Fetch ad insights
     *
     * @param \FacebookAds\Object\Ad $ad
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    private function fetchInsights(Ad $ad)
    {
        return collect($ad->getInsights(Insightful::FIELDS, [
            'time_range' => [
                'since' => $this->date->toDateString(),
                'until' => $this->date->toDateString()
            ],
            'level'      => Insightful::MODE_AD,
            'breakdowns' => 'age',
        ]))->map(function (AdsInsights $insight) {
            return $insight->exportAllData();
        });
    }

    /**
     * Determine ad offer using campaign id
     *
     * @param \FacebookAds\Object\Ad $ad
     *
     * @return int|null
     */
    private function getOffer(Ad $ad)
    {
        return optional(Campaign::find($ad->exportAllData()['campaign_id']))->offer_id;
    }
}
