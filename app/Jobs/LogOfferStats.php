<?php

namespace App\Jobs;

use App\Binom\Statistic;
use App\Offer;
use App\OfferStatisticsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LogOfferStats implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * @var Offer
     */
    protected $offer;

    /**
     * CreateOfficeTrend constructor.
     *
     * @param Offer $offer
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        OfferStatisticsLog::create($this->statistics($this->offer));
    }

    /**
     * Prepare offer data for storing.
     *
     * @param Offer $offer
     *
     * @return mixed
     */
    protected function statistics(Offer $offer)
    {
        $offerWithStats = Offer::select(['id','name'])
            ->withCurrentImpressionsCount()
            ->withCurrentClicksCount()
            ->withCurrentAccountsCount()
            ->withCurrentCost()
            ->withCurrentFbLeadsCount()
            ->withCurrentCpl()
            ->withCurrentCpm()
            ->withCurrentCpc()
            ->withCurrentCtr()
            ->withCurrentOwnLeadsCount()
            ->withCurrentOwnValidLeadsCount()
            ->withCurrentAffiliateLeadsCount()
            ->withCurrentAffiliateValidLeadsCount()
            ->find($offer->id);

        $binomStats = $this->getBinomStatsForOffer($offer);


        return [
            'datetime'              => now()->toDateTimeString(),
            'offer_id'              => $offer->id,
            'impressions'           => $offerWithStats->current_impressions,
            'clicks'                => $offerWithStats->current_clicks,
            'accounts'              => $offerWithStats->current_accounts,
            'cpm'                   => $offerWithStats->current_facebook_cpm,
            'cpc'                   => $offerWithStats->current_facebook_cpc,
            'ctr'                   => $offerWithStats->current_facebook_ctr,
            'fb_leads'              => $offerWithStats->current_facebook_leads,
            'fb_cpl'                => $offerWithStats->current_facebook_cpl,
            'cost'                  => $offerWithStats->current_facebook_cost,
            'valid_leads'           => $offerWithStats->accepted,
            'leads'                 => $offerWithStats->current_all_own_leads,
            'affiliate_valid_leads' => $offerWithStats->current_valid_affiliate_leads,
            'affiliate_leads'       => $offerWithStats->current_all_affiliate_leads,
            'binom_clicks'          => $binomStats->clicks,
            'binom_unique_clicks'   => $binomStats->unique_clicks,
            'lp_views'              => $binomStats->lp_views,
            'lp_clicks'             => $binomStats->lp_clicks,
            'binom_leads'           => $binomStats->leads,
            'lp_ctr'                => $binomStats->lp_ctr,
            'offer_cr'              => $binomStats->offer_cr,
            'cr'                    => $binomStats->cr,
            'binom_cpl'             => round(
                zero_safe_division($offerWithStats->current_facebook_cost, $binomStats->leads),
                2
            ),
        ];
    }

    /**
     * Fetch binom statistics for offer
     *
     * @param \App\Offer $offer
     *
     * @return \App\Binom\Statistic|\Illuminate\Database\Eloquent\Builder
     */
    protected function getBinomStatsForOffer(Offer $offer)
    {
        return Statistic::forOffer($offer)
            ->whereDate('date', now())
            ->select([
                DB::raw('sum(clicks) as clicks'),
                DB::raw('sum(lp_clicks) as lp_clicks'),
                DB::raw('sum(lp_views) as lp_views'),
                DB::raw('sum(unique_clicks) as unique_clicks'),
                DB::raw('sum(leads) as leads'),
                DB::raw('ROUND(sum(lp_clicks::decimal) / nullif(sum(lp_views::decimal),0) * 100, 2) as lp_ctr'),
                DB::raw('ROUND(sum(leads::decimal) / nullif(sum(lp_clicks::decimal),0) * 100, 2) as offer_cr'),
                DB::raw('ROUND(sum(leads::decimal) / nullif(sum(clicks::decimal),0) * 100, 2) as cr'),
            ])->first();
    }
}
