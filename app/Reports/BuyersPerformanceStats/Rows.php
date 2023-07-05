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
use App\Offer;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Rows implements Arrayable
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
     * Collection of offers
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

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
        $this->insights       =  $insights;
        $this->deposits       = $deposits;
        $this->traffic        = $traffic;
        $this->hideFacebook   = $hideFacebook;
        $this->offers         = $this->getOffers();
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
     * @return \App\Reports\BuyersPerformanceStats\Rows
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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function toArray()
    {
        return $this->buyers
            ->map(function (User $buyer) {
                $row = array_merge(
                    $this->defaults($buyer),
                    $this->conversions($buyer),
                    $this->aggregates($buyer)
                );

                return $this->hideFacebook ? Arr::except($row, Fields::fbKeys()) : $row;
            });
    }


    /**
     * Get default values for report
     *
     * @param User $buyer
     *
     * @return array
     */
    protected function defaults($buyer)
    {
        $insights = $this->insights->whereIn('account_id', $buyer->accounts->pluck('account_id'));

        return [
            Fields::NAME        => $buyer->name ?: 'Unknown',
            Fields::OFFER       => optional($this->offers->where('id', optional($insights->first())->offer_id)->first())->name,
            Fields::RK_COUNT    => $buyer->accounts->count(),
            Fields::IMPRESSIONS => $insights->sum(Fields::IMPRESSIONS),
            Fields::CLICKS      => Clicks::make($insights)->metric(),
            Fields::CPM         => CPM::make($insights)->metric(),
            Fields::CPC         => CPC::make($insights)->metric(),
            Fields::CTR         => CTR::make($insights)->metric(),
        ];
    }

    /**
     * Get conversion values for report
     *
     * @param User $buyer
     *
     * @return array
     */
    protected function conversions(User $buyer)
    {
        $insights = $this->insights->whereIn('account_id', $buyer->accounts->pluck('account_id'));
        $traffic  = $this->traffic->whereIn('account_id', $buyer->accounts->pluck('account_id'));

        return [
            Fields::BCLICKS               => $traffic->sum('clicks') ,
            Fields::LPVIEWS               => $traffic->sum('lp_views') ,
            Fields::LPCLICKS              => $traffic->sum('lp_clicks'),
            Fields::CRPL                  => sprintf(
                "%s %%",
                $this->percentage($traffic->sum('lp_clicks'), $traffic->sum('lp_views'))
            ),
            Fields::CRLP                  => sprintf(
                "%s %%",
                $this->percentage($traffic->sum('leads'), $traffic->sum('lp_clicks'))
            ),
            Fields::CR                    => sprintf(
                "%s %%",
                $this->percentage($traffic->sum('leads'), $traffic->sum('clicks'))
            ),
            Fields::LEADS                 => LeadsCount::make($insights)->metric(),
            Fields::BLEADS                => $traffic->sum('leads'),
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
     * Get offers
     *
     * @return Offer[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getOffers()
    {
        return Offer::query()->whereIn('id', $this->insights->pluck('offer_id')->unique())->get();
    }

    /**
     * Get aggregate values for report
     *
     * @param User $buyer
     *
     * @return array
     */
    protected function aggregates($buyer)
    {
        $insights = $this->insights->whereIn('account_id', $buyer->accounts->pluck('account_id'));
        $traffic  = $this->traffic->whereIn('account_id', $buyer->accounts->pluck('account_id'));
        $deposits = $this->deposits->whereIn('account_id', $buyer->accounts->pluck('account_id'));

        return [
            Fields::FTD          => $deposits->count(),
            // getLeadsToFtd
            Fields::FTD_PERCENT  => sprintf(
                "%s %%",
                $this->percentage($deposits->count(), $insights->sum(Fields::LEADS))
            ),
            Fields::FTD_COST     => $this->getFtdCost($insights, $deposits->count()),
            // binomLeadsToFtd
            Fields::BFTD_PERCENT => sprintf("%s %%", $this->percentage($deposits->count(), $traffic->sum('leads'))),
            Fields::REVENUE      => sprintf("\$ %s", round($this->getRevenue($deposits->count()), 2)),
            Fields::CPL          => CPL::make($insights)->metric(),
            Fields::BCPL         => $this->getBinomCpl($insights, $traffic),
            Fields::COST         => Cost::make($insights)->metric(),
            Fields::PROFIT       => sprintf("\$ %s", round($this->getProfit($insights, $deposits->count()), 2)),
            Fields::ROI          => sprintf("%s %%", round($this->getRoi($insights, $deposits->count()), 2)),
        ];
    }

    /**
     * Get deposit cost
     *
     * @param \Illuminate\Database\Eloquent\Collection $insights
     * @param int                                      $deposits
     *
     * @return float|int
     */
    protected function getFtdCost($insights, $deposits)
    {
        if ($deposits) {
            return sprintf("\$ %s", round(Cost::make($insights)->value() / $deposits, 2));
        }

        return 0;
    }

    /**
     * Get Binom CPL
     *
     * @param \Illuminate\Database\Eloquent\Collection $insights
     * @param \Illuminate\Database\Eloquent\Collection $traffic
     *
     * @return float|int|string
     */
    protected function getBinomCpl($insights, $traffic)
    {
        $leads = $traffic->sum('leads');

        if ($leads) {
            return sprintf("\$ %s", round(Cost::make($insights)->value() / $leads, 2));
        }

        return 0;
    }

    /**
     * Get revenue
     * Depends on office model.
     *
     * TODO:// make this configurable
     *
     * @param int $deposits
     *
     * @return string
     */
    protected function getRevenue($deposits)
    {
        return $deposits * 400;
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @param \Illuminate\Support\Collection $insights
     * @param $deposits
     *
     * @return string
     */
    protected function getProfit($insights, $deposits)
    {
        return $this->getRevenue($deposits) - Cost::make($insights)->value();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @param \Illuminate\Support\Collection $insights
     * @param int                            $deposits
     *
     * @return string
     */
    protected function getRoi($insights, $deposits)
    {
        if (Cost::make($insights)->value()) {
            return ($this->getProfit($insights, $deposits) / Cost::make($insights)->value()) * 100;
        }

        return 0;
    }
}
