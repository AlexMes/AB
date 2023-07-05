<?php

namespace App\Binom\Jobs;

use App\Binom\Campaign;
use App\Binom\Statistic;
use App\ManualCampaign;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CacheCampaignStatistics implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Binom campaign instance
     *
     * @var \App\Binom\Campaign
     */
    protected $campaign;

    /**
     * Date for the cacher
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * CacheCampaignStatistics constructor.
     *
     * @param \App\Binom\Campaign $campaign
     * @param $date
     */
    public function __construct(Campaign $campaign, $date)
    {
        $this->campaign = $campaign;
        $this->date     = $date;
    }

    /**
     * Handle the job
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldRun()) {
            $this->cache();
        }
    }

    /**
     * Determine is caching should be done
     *
     * @return bool
     */
    public function shouldRun(): bool
    {
        if ($this->campaign->binom_id !== null) {
            return $this->campaign->binom->isEnabled();
        }

        return false;
    }

    /**
     * Actually do the work
     *
     * @throws \Exception
     */
    public function cache()
    {
        $this->campaign->getStatistics($this->date)
            ->pipe(function (Collection $collection) {
                $campaign = null;
                $adset    = null;

                return $collection->map(function ($item) use (&$campaign, &$adset) {
                    if (!is_array($item)) {
                        return null;
                    }

                    if (is_array($item) && ! array_key_exists('level', $item)) {
                        return null;
                    }

                    if (is_array($item) && array_key_exists('level', $item) && $item['level'] == 1) {
                        $campaign = $item;

                        return null;
                    } elseif (is_array($item) && array_key_exists('level', $item) && $item['level'] == 2) {
                        $adset = $item;

                        return null;
                    }

                    return [
                        'name'               => $campaign['name'],
                        'utm_term'           => $adset['name'],
                        'utm_content'        => $item['name'],
                        'clicks'             => $item['clicks'],
                        'lp_clicks'          => $item['lp_clicks'],
                        'lp_views'           => $item['lp_views'],
                        'unique_clicks'      => $item['unique_clicks'],
                        'unique_camp_clicks' => $item['unique_camp_clicks'],
                        'leads'              => $item['leads'],
                        'cost'               => $item['cost']
                    ];
                })->reject(fn ($item) => $item === null);
            })
            ->each(function ($statistic) {
                $this->exists($statistic)
                    ? $this->update($statistic)
                    : $this->create($statistic);
            });
    }

    /**
     * Determine is insight already cached
     *
     * @param array $statistic
     *
     * @return bool
     */
    protected function exists($statistic)
    {
        return Statistic::query()
            ->where('binom_id', $this->campaign->binom_id)
            ->whereDate('date', $this->date->toDateString())
            ->where('campaign_id', $this->campaign->id)
            ->where('utm_source', $statistic['name'])
            ->where('utm_term', $statistic['utm_term'])
            ->where('utm_content', $statistic['utm_content'])
            ->exists();
    }

    /**
     * Save new stats
     *
     * @param array $statistic
     *
     * @return void
     */
    protected function create($statistic)
    {
        Statistic::create(array_merge(
            [
                'binom_id'     => $this->campaign->binom_id,
                'date'         => $this->date->toDateString(),
                'campaign_id'  => $this->campaign->id,
                'utm_source'   => $statistic['name'],
                'utm_campaign' => Str::contains($statistic['name'], 'campaign-')
                    ? Str::afterLast($statistic['name'], 'campaign-')
                    : Str::afterLast($statistic['name'], '-'),
                'utm_term'    => $statistic['utm_term'],
                'utm_content' => $statistic['utm_content'],
            ],
            $this->internalBindings($statistic),
            Arr::only(
                $statistic,
                ['clicks','lp_clicks','lp_views','unique_clicks','unique_camp_clicks','leads','cost']
            )
        ));
    }

    /**
     * Update existing stats
     *
     * @param array $statistics
     *
     * @return void
     */
    protected function update($statistics)
    {
        DB::table('binom_statistics')
            ->where('binom_id', $this->campaign->binom_id)
            ->whereDate('date', $this->date->toDateString())
            ->where('campaign_id', $this->campaign->id)
            ->where('utm_source', $statistics['name'])
            ->where('utm_term', $statistics['utm_term'])
            ->where('utm_content', $statistics['utm_content'])
            ->update(Arr::only(
                $statistics,
                ['clicks','lp_clicks','lp_views','unique_clicks','unique_camp_clicks','leads','cost']
            ));
    }

    /**
     * Detect campaign id, account id, and user
     *
     * @param array $statistics
     *
     * @return array
     */
    protected function internalBindings($statistics)
    {
        $result = [
            'fb_adset_id' => optional(\App\Facebook\AdSet::whereName($statistics['utm_term'])->first())->id,
            'fb_ad_id'    => optional(\App\Facebook\Ad::whereName($statistics['utm_content'])->first())->id,
        ];

        $utmSource         = Str::limit($statistics['name'], 255);
        $campaign          = \App\Facebook\Campaign::whereName($utmSource)->first();
        $result['user_id'] = $this->userFromTag($utmSource);

        if ($campaign === null) {
            $campaign = ManualCampaign::whereName($utmSource)->first();
        }

        if ($campaign !== null) {
            $result['fb_campaign_id'] = $campaign->id;
            $result['account_id']     = $campaign->account_id;
            $result['user_id']        = $result['user_id'] ?? optional($campaign->account->user)->id;
        }

        return $result;
    }

    /**
     * @param $utmSource
     *
     * @return void
     */
    protected function userFromTag($utmSource)
    {
        return User::query()
            ->whereNotIn('role', [User::GAMBLE_ADMIN, User::GAMBLER])
            ->whereNotNull('binomTag')
            ->pluck('binomTag', 'id')
            ->sortByDesc(fn ($tag) => strlen($tag))
            ->filter(fn ($user)    => Str::contains(Str::lower($utmSource), Str::lower($user)))
            ->keys()
            ->first();
    }
}
