<?php

namespace App\Binom\Jobs;

use App\Binom\Campaign;
use App\Binom\TelegramStatistic;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CacheTelegramStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $this->getStatistic()
            ->groupBy('name')
            ->map(function (Collection $group, $name) {
                return [
                    'name'               => $name,
                    'clicks'             => $group->sum('clicks'),
                    'lp_clicks'          => $group->sum('lp_clicks'),
                    'lp_views'           => $group->sum('lp_views'),
                    'unique_clicks'      => $group->sum('unique_clicks'),
                    'unique_camp_clicks' => $group->sum('unique_camp_clicks'),
                    'leads'              => $group->sum('leads')
                ];
            })
            ->each(function ($statistic) {
                $this->exists($statistic)
                    ? $this->update($statistic)
                    : $this->create($statistic);
            });
    }

    /**
     * Load statistics for specific date and campaign
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function getStatistic()
    {
        return collect(app('binom')->getStatistics($this->campaign, [
            'date_s' => $this->date->toDateString(),
            'date_e' => $this->date->toDateString(),
        ]));
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
        return TelegramStatistic::query()
            ->whereDate('date', $this->date->toDateString())
            ->where('campaign_id', $this->campaign->id)
            ->where('utm_source', $statistic['name'])
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
        TelegramStatistic::create(array_merge(
            [
                'date'           => $this->date->toDateString(),
                'campaign_id'    => $this->campaign->id,
                'utm_source'     => $statistic['name'],
                'utm_campaign'   => Str::contains($statistic['name'], 'campaign-')
                    ? Str::afterLast($statistic['name'], 'campaign-')
                    : $statistic['name'],
            ],
            $this->internalBindings($statistic['name']),
            Arr::only(
                $statistic,
                ['clicks','lp_clicks','lp_views','unique_clicks','unique_camp_clicks','leads']
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
        TelegramStatistic::query()
            ->whereDate('date', $this->date->toDateString())
            ->where('campaign_id', $this->campaign->id)
            ->where('utm_source', $statistics['name'])
            ->update(Arr::only(
                $statistics,
                ['clicks','lp_clicks','lp_views','unique_clicks','unique_camp_clicks','leads']
            ));
    }

    /**
     * Detect campaign id, account id, and user
     *
     * @param string $utmSource
     *
     * @return array
     */
    protected function internalBindings($utmSource)
    {
        $campaign = \App\Facebook\Campaign::whereName($utmSource)->first();

        if ($campaign !== null) {
            return [
                'fb_campaign_id' => $campaign->id,
                'account_id'     => $campaign->account_id,
                'user_id'        => optional($campaign->account->profile)->user_id ?? $this->userFromTag($utmSource),
            ];
        }

        return [];
    }

    /**
     * @param $utmSource
     *
     * @return void
     */
    protected function userFromTag($utmSource)
    {
        $user = User::query()
            ->whereNotNull('binomTag')
            ->get()
            ->sortByDesc(function ($user, $key) {
                return strlen($user->binomTag);
            })->filter(function ($user) use ($utmSource) {
                return Str::contains(Str::lower($utmSource), Str::kebab(Str::lower($user->name)));
            })->first();

        return optional($user)->id;
    }
}
