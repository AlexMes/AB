<?php

namespace App\Queries;

use App\Binom\TelegramStatistic;
use App\TelegramChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TelegramPerformanceTraffic
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
     * @var \App\Binom\TelegramStatistic
     */
    protected $statistics;

    /**
     * Performance Insights Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->statistics = TelegramStatistic::query()
            ->unless(auth()->user()->isAdmin(), function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw('1'))
                        ->from('binom_campaigns')
                        ->whereColumn('binom_telegram_statistics.campaign_id', 'binom_campaigns.id')
                        ->whereIn('binom_campaigns.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
                });
            });
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
     * @return \app\Queries\TelegramPerformanceTraffic
     */
    public static function fetch()
    {
        return new self();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \app\Queries\TelegramPerformanceTraffic
     */
    public function forPeriod($since, $until)
    {
        $this->statistics->whereBetween('date', [$since->toDateString(), $until->toDateString()]);

        return $this;
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
     * Filter report for specific subject
     *
     * @param string $subject
     *
     * @return \App\Queries\TelegramPerformanceTraffic
     */
    public function forSubject($subject = null)
    {
        if ($subject != null) {
            $channels = TelegramChannel::where('subject_id', $subject)->pluck('name');
            $this->statistics->whereIn('utm_campaign', $channels);
        }

        return $this;
    }
}
