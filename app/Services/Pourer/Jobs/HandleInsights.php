<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\Campaign;
use App\Insights;
use App\User;
use FacebookAds\Object\Fields\AdFields;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;

class HandleInsights implements ShouldQueue
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
    public $tries = 2;

    protected array $insights;

    /**
     * @var \App\User[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $userTags;

    /**
     * CollectInsights constructor.
     *
     * @param array $insights
     */
    public function __construct(array $insights)
    {
        $this->insights = $insights;
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
        } catch (\Throwable $exception) {
            // lemme solve shit. todo://drop logging once solved
        }
    }

    /**
     * Load insights from Facebook API
     *
     * @return \Illuminate\Support\Collection
     */
    protected function insights()
    {
        return collect($this->insights)
            ->map(function ($insight) {
                Log::info(sprintf(
                    'Aya, fucker, i got insights for ya. Here it is, ad id is %s \\n campaign id is %s \\  adset id is %s \\n Spend is equal to %s damn franklins.',
                    $insight['ad_id'],
                    $insight['campaign_id'],
                    $insight['adset_id'],
                    $insight['spend']
                ));

                return [
                    'date'        => $insight['date'],
                    'account_id'  => $insight['account_id'],
                    'campaign_id' => $insight['campaign_id'],
                    'adset_id'    => $insight['adset_id'],
                    'ad_id'       => $insight['ad_id'],
                    'offer_id'    => $this->getOffer($insight),
                    'reach'       => $insight['reach'],
                    'impressions' => $insight['impressions'],
                    'spend'       => $insight['spend'],
                    'cpm'         => $insight['cpm'] ?? 0,
                    'cpc'         => $insight['cpc'] ?? 0,
                    'ctr'         => $insight['ctr'] ?? 0,
                    'clicks'      => $insight['clicks'],
                    'cpl'         => $this->cpl($insight) ?? 0,
                    'frequency'   => $insight['frequency'] ?? 0.00,
                    'leads_cnt'   => collect($insight['actions'] ?? [])->filter(function ($action) {
                        return $action['action_type'] === 'offsite_conversion.fb_pixel_lead' || $action['action_type'] === 'lead';
                    })->first()['value'] ?? 0,
                    'link_clicks' => collect($insight['actions'] ?? [])->filter(function ($action) {
                        return $action['action_type'] === 'link_click';
                    })->first()['value'] ?? 0,
                    'actions' => $insight['actions'] ?? null,
                    'user_id' => $this->getBuyer($insight)
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
        DB::table('facebook_cached_insights')
            ->whereDate('date', $insight['date'])
            ->where('account_id', $insight['account_id'])
            ->where('campaign_id', $insight['campaign_id'])
            ->where('adset_id', $insight['adset_id'])
            ->where('ad_id', $insight['ad_id'])
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
        Insights::create($insight);
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
        return Insights::query()
            ->whereDate('date', $insight['date'])
            ->where('account_id', $insight['account_id'])
            ->where('campaign_id', $insight['campaign_id'])
            ->where('adset_id', $insight['adset_id'])
            ->where('ad_id', $insight['ad_id'])
            ->first() === null;
    }

    /**
     * Determine ad offer using campaign id
     *
     * @param array $insight
     *
     * @return int|null
     */
    private function getOffer(array $insight)
    {
        return optional(Campaign::find($insight['campaign_id']))->offer_id;
    }

    /**
     * Get buyer from the ad
     *
     * @param array $insight
     */
    protected function getBuyer(array $insight)
    {
        Log::info('Hola, hola, yolo, here we go, trying to detect buyer of inisight, ha. ');
        $campaign = Campaign::find($insight['campaign_id']);
        if ($campaign === null) {
            return null;
        }

        return $this->getBuyerForCampaign($campaign);
    }

    /**
     * Find buyer using binom tag
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return \App\User|null
     */
    protected function getBuyerForCampaign(Campaign $campaign)
    {
        $buyer = $this->getBuyerUsingTag($campaign);
        if ($buyer !== null) {
            return $buyer->id;
        }

        return $this->getBuyerFromProfile($campaign);
    }

    /**
     * Resolve buyer id from tag in campaign
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return mixed
     */
    protected function getBuyerUsingTag(Campaign $campaign)
    {
        return $this->userTags()
            ->filter(fn ($user) => Str::contains(Str::lower($campaign->name), Str::lower($user->binomTag)))
            ->first();
    }

    /**
     * @return \App\User[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function userTags()
    {
        if (! $this->userTags) {
            $this->userTags = User::get(['id','binomTag'])->sortBy(fn ($user) => strlen($user->binomTag));
        }

        return $this->userTags;
    }

    /**
     * Get buyer from the profile
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return \App\User|null
     */
    protected function getBuyerFromProfile(Campaign $campaign)
    {
        return optional(optional($campaign->account)->profile)->user_id;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function retryUntil()
    {
        return now()->addSeconds(60);
    }
}
