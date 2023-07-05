<?php

namespace App\Reports\Performance;

use App\Insights;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\Impressions;
use App\Metrics\Facebook\LeadsCount;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Summary implements Arrayable
{
    /**
     * Results collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $deposits;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $manualInsights;

    /**
     * Report level
     *
     * @var string
     */
    protected $level;

    /**
     * Cached binom statistics
     *
     * @var \Illuminate\Support\Collection | null
     */
    protected $traffic;

    /**
     * @var bool
     */
    protected bool $hideFacebook;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     * @param bool                           $hideFacebook
     */
    public function __construct(Collection $insights, Collection $manualInsights, Collection $deposits, $level, $traffic = null, $hideFacebook = false)
    {
        $this->insights       = $insights;
        $this->manualInsights = $manualInsights;
        $this->deposits       = $deposits->count();
        $this->level          = $level;
        $this->traffic        = $traffic;
        $this->hideFacebook   = $hideFacebook;
    }

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     * @param mixed                          $hideFb
     *
     * @return \App\Reports\Performance\Summary
     */
    public static function build(Collection $insights, Collection $manualInsights, Collection $deposits, $level, $traffic = null, $hideFb = false)
    {
        return new self($insights, $manualInsights, $deposits, $level, $traffic, $hideFb);
    }

    /**
     * Get array representation of summary
     *
     * @return array
     */
    public function toArray()
    {
        $row = array_merge(
            $this->defaults(),
            $this->conversions(),
            $this->aggregates()
        );

        return $this->hideFacebook ? Arr::except($row, Fields::fbKeys()) : $row;
    }

    /**
     * Get default values for report
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            Fields::NAME                  => 'ИТОГО',
            Fields::OFFER                 => '',
            Fields::RK_COUNT              => $this->insights->unique('account_id')->count(),
            Fields::IMPRESSIONS           => Impressions::make($this->insights)->metric(),
            Fields::CLICKS                => Clicks::make($this->insights)->metric(),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
        ];
    }

    /**
     * Get conversion values for report
     *
     *
     * @return array
     */
    protected function conversions(): array
    {
        if ($this->level === Report::LEVEL_ACCOUNT) {
            return [
                Fields::BCLICKS               => $this->traffic->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->sum('lp_views') ,
                Fields::LPCLICKS              => $this->traffic->sum('lp_clicks'),
                Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
                Fields::MANUAL_LEADS          => LeadsCount::make($this->manualInsights)->metric(),
                Fields::BLEADS                => $this->traffic->sum('leads'),
            ];
        }

        if ($this->level === Report::LEVEL_ADSET) {
            return [
                Fields::BCLICKS               => $this->traffic->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->sum('lp_views') ,
                Fields::LPCLICKS              => $this->traffic->sum('lp_clicks'),
                Fields::CRPL                  => sprintf("%s %%", round($this->getLpCtr(), 2)),
                Fields::CRLP                  => sprintf("%s %%", round($this->getOfferCr(), 2)),
                Fields::CR                    => sprintf("%s %%", round($this->getCr(), 2)),
                Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
                Fields::BLEADS                => $this->traffic->sum('leads'),
            ];
        }

        return [
            Fields::BCLICKS               => $this->traffic->sum('clicks') ,
            Fields::LPVIEWS               => $this->traffic->sum('lp_views') ,
            Fields::LPCLICKS              => $this->traffic->sum('lp_clicks'),
            Fields::CRPL                  => sprintf("%s %%", round($this->getLpCtr(), 2)),
            Fields::CRLP                  => sprintf("%s %%", round($this->getOfferCr(), 2)),
            Fields::CR                    => sprintf("%s %%", round($this->getCr(), 2)),
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::MANUAL_LEADS          => LeadsCount::make($this->manualInsights)->metric(),
            Fields::BLEADS                => $this->traffic->sum('leads'),
        ];
    }

    /**
     * Get CRPL (LP CTR)
     *
     * @return float|int
     */
    protected function getLpCtr()
    {
        if ($this->traffic->sum('lp_views')) {
            return ($this->traffic->sum('lp_clicks') / $this->traffic->sum('lp_views')) * 100;
        }

        return 0;
    }

    /**
     * Get CRLP (offer CR)
     *
     * @return int
     */
    protected function getOfferCr()
    {
        if ($this->traffic->sum('lp_clicks')) {
            return ($this->traffic->sum('leads') / $this->traffic->sum('lp_clicks')) * 100;
        }

        return 0;
    }

    /**
     * Get CRLP (offer CR)
     *
     * @return int
     */
    protected function getCr()
    {
        if ($this->traffic->sum('clicks')) {
            return ($this->traffic->sum('leads') / $this->traffic->sum('clicks')) * 100;
        }

        return 0;
    }

    /**
     * Get aggregate values for report
     *
     *
     * @return array
     */
    protected function aggregates(): array
    {
        return [
            Fields::FTD                   => $this->deposits,
            Fields::FTD_PERCENT           => sprintf("%s %%", round($this->getLeadsToFtd(), 2)),
            Fields::FTD_COST              => sprintf("\$ %s", round($this->getFtdCost(), 2)),
            Fields::BFTD_PERCENT          => sprintf(
                "%s %%",
                round($this->binomLeadsToFtd(), 2)
            ),
            Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL                   => CPL::make($this->insights)->metric(),
            Fields::BCPL                  => $this->getBinomCpl(),
            Fields::COST                  => Cost::make($this->insights)->metric(),
            Fields::PROFIT                => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                   => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    /**
     * Get leads to FTD percentage
     *
     * @return false|float|int
     */
    protected function binomLeadsToFtd()
    {
        $leads = $this->traffic->sum('leads');

        if ($leads) {
            return ($this->deposits / $leads) * 100;
        }

        return 0;
    }

    /**
     * Get Binom CPL
     *
     * @return float|int|null
     */
    protected function getBinomCpl()
    {
        if ($this->traffic === null) {
            return 0;
        }

        $leads = $this->traffic->sum('leads');

        if ($leads) {
            return sprintf("\$ %s", round(Cost::make($this->insights)->value() / $leads, 2));
        }

        return null;
    }

    /**
     * Get leads to FTD percentage
     *
     * @return false|float|int
     */
    protected function getLeadsToFtd()
    {
        if ($this->insights->sum(Fields::LEADS) === 0) {
            return 0;
        }

        return ($this->deposits / $this->insights->sum(Fields::LEADS)) * 100;
    }

    /**
     * Get revenue
     * Depends on office model.
     *
     * TODO:// make this configurable
     *
     * @return string
     */
    protected function getRevenue()
    {
        return $this->deposits * 400;
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return string
     */
    protected function getProfit()
    {
        return $this->getRevenue() - Cost::make($this->insights)->value();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return string
     */
    protected function getRoi()
    {
        if (($costs = Cost::make($this->insights)->value())) {
            return ($this->getProfit() / $costs) * 100;
        }

        return 0;
    }

    /**
     * @return float|int
     */
    protected function getFtdCost()
    {
        if ($this->deposits) {
            return Cost::make($this->insights)->value() / $this->deposits;
        }

        return 0;
    }
}
