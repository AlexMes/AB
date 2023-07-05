<?php

namespace App\Reports\BuyersPerformanceStats;

use App\Binom\Statistic;
use App\Insights;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\LeadsCount;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Summary implements Arrayable
{
    /**
     * Buyers collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $buyers;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Collection of deposits
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $deposits;

    /**
     * Collection of binom statistic
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $traffic;

    /**
     * Defines when to hide Facebook traffic params
     *
     * @var bool
     */
    protected bool $hideFacebook = false;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Database\Eloquent\Collection $buyers
     * @param \Illuminate\Database\Eloquent\Collection $insights
     * @param \Illuminate\Database\Eloquent\Collection $deposits
     * @param \Illuminate\Database\Eloquent\Collection $traffic
     * @param bool                                     $hideFacebook
     */
    public function __construct(
        $buyers,
        $insights,
        $deposits,
        $traffic,
        $hideFacebook
    ) {
        $this->buyers         = $buyers;
        $this->insights       = $insights;
        $this->deposits       = $deposits;
        $this->traffic        = $traffic;
        $this->hideFacebook   = $hideFacebook;
    }

    /**
     * Named constructor

     *
     * @param $buyers
     * @param $insights
     * @param $deposits
     * @param $traffic
     * @param bool $hideFacebook
     *
     * @return \App\Reports\BuyersPerformanceStats\Summary
     */
    public static function build(
        $buyers,
        $insights,
        $deposits,
        $traffic,
        $hideFacebook = false
    ) {
        return new static($buyers, $insights, $deposits, $traffic, $hideFacebook);
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
    protected function defaults()
    {
        return [
            Fields::NAME        => 'ИТОГО',
            Fields::OFFER       => '',
            Fields::RK_COUNT    => $this->buyers->flatMap->accounts->count(),
            Fields::IMPRESSIONS => $this->insights->sum(Fields::IMPRESSIONS),
            Fields::CLICKS      => Clicks::make($this->insights)->metric(),
            Fields::CPM         => CPM::make($this->insights)->metric(),
            Fields::CPC         => CPC::make($this->insights)->metric(),
            Fields::CTR         => CTR::make($this->insights)->metric(),
        ];
    }

    /**
     * Get conversion values for report
     *
     * @return array
     */
    protected function conversions()
    {
        return [
            Fields::BCLICKS               => $this->traffic->sum('clicks') ,
            Fields::LPVIEWS               => $this->traffic->sum('lp_views') ,
            Fields::LPCLICKS              => $this->traffic->sum('lp_clicks'),
            Fields::CRPL                  => sprintf(
                "%s %%",
                $this->percentage($this->traffic->sum('lp_clicks'), $this->traffic->sum('lp_views'))
            ),
            Fields::CRLP                  => sprintf(
                "%s %%",
                $this->percentage($this->traffic->sum('leads'), $this->traffic->sum('lp_clicks'))
            ),
            Fields::CR                    => sprintf(
                "%s %%",
                $this->percentage($this->traffic->sum('leads'), $this->traffic->sum('clicks'))
            ),
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::BLEADS                => $this->traffic->sum('leads'),
        ];
    }

    protected function percentage($one, $two)
    {
        if ($two) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
    }

    /**
     * Get aggregate values for report
     *
     * @return array
     */
    protected function aggregates()
    {
        return [
            Fields::FTD          => $this->deposits->count(),
            // getLeadsToFtd
            Fields::FTD_PERCENT  => sprintf(
                "%s %%",
                $this->percentage($this->deposits->count(), $this->insights->sum(Fields::LEADS))
            ),
            Fields::FTD_COST     => $this->getFtdCost(),
            // binomLeadsToFtd
            Fields::BFTD_PERCENT => sprintf(
                "%s %%",
                $this->percentage($this->deposits->count(), $this->traffic->sum('leads'))
            ),
            Fields::REVENUE      => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL          => CPL::make($this->insights)->metric(),
            Fields::BCPL         => $this->getBinomCpl(),
            Fields::COST         => Cost::make($this->insights)->metric(),
            Fields::PROFIT       => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI          => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    /**
     * Get deposit cost
     *
     * @return float|int|string
     */
    protected function getFtdCost()
    {
        if ($this->deposits->count()) {
            return sprintf("\$ %s", round(Cost::make($this->insights)->value() / $this->deposits->count(), 2));
        }

        return 0;
    }

    /**
     * Get Binom CPL
     *
     * @return float|int|string
     */
    protected function getBinomCpl()
    {
        $leads = $this->traffic->sum('leads');

        if ($leads) {
            return sprintf("\$ %s", round(Cost::make($this->insights)->value() / $leads, 2));
        }

        return 0;
    }

    /**
     * Get revenue
     * Depends on office model.
     *
     * TODO:// make this configurable
     *
     * @return float|int|string
     */
    protected function getRevenue()
    {
        return $this->deposits->count() * 400;
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return float|int|string
     */
    protected function getProfit()
    {
        return $this->getRevenue() - Cost::make($this->insights)->value();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return float|int|string
     */
    protected function getRoi()
    {
        if (Cost::make($this->insights)->value()) {
            return ($this->getProfit() / Cost::make($this->insights)->value()) * 100;
        }

        return 0;
    }
}
