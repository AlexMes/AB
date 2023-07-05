<?php

namespace App\Reports\FacebookPerformance;

use App\Insights;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\LeadsCount;
use App\Offer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Rows implements Arrayable
{
    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Collection of deposits
     *
     * @var \Illuminate\Support\Collection
     */
    protected $deposits;

    /**
     * Level of subtotal
     *
     * @var string
     */
    protected $level;

    /**
     * @var null
     */
    protected $traffic;

    /**
     * @var null
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
     * @param Collection                     $insights
     * @param \Illuminate\Support\Collection $deposits
     * @param string                         $level
     * @param Collection|null                $traffic
     * @param bool                           $hideFacebook
     */
    public function __construct(
        $insights,
        $deposits,
        $level = Report::LEVEL_AD,
        $traffic = null,
        $hideFacebook = false
    ) {
        $this->insights       =  $insights;
        $this->deposits       = $deposits;
        $this->level          = $level;
        $this->traffic        = $traffic;
        $this->offers         = $this->getOffers();
        $this->hideFacebook   = $hideFacebook;
    }

    /**
     * Named constructor
     *
     * @param $insights
     * @param $deposits
     * @param mixed $level
     * @param null  $traffic
     * @param bool  $hideFacebook
     *
     * @return \App\Reports\FacebookPerformance\Rows
     */
    public static function build(
        $insights,
        $deposits,
        $level = Report::LEVEL_AD,
        $traffic = null,
        $hideFacebook = false
    ) {
        return new static($insights, $deposits, $level, $traffic, $hideFacebook);
    }

    /**
     * Get array representation of summary
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function toArray()
    {
        return $this->insights
            ->groupBy('name')
            ->map(function (Collection $group, $name) {
                $row = array_merge(
                    $this->defaults($group, $name),
                    $this->conversions($group, $name),
                    $this->aggregates($group, $name)
                );

                return $this->hideFacebook ? Arr::except($row, Fields::fbKeys()) : $row;
            });
    }


    /**
     * Get default values for report
     *
     * @param \Illuminate\Support\Collection $group
     * @param string                         $name
     *
     * @return array
     */
    protected function defaults($group, $name)
    {
        return[
            Fields::NAME        => $name ? $name : 'Unknown',
            Fields::OFFER       => $this->offers->where('id', $group->first()->offer_id)->first()->name,
            Fields::RK_COUNT    => $group->unique('account_id')->count(),
            Fields::IMPRESSIONS => $group->sum(Fields::IMPRESSIONS),
            Fields::CLICKS      => Clicks::make($group)->metric(),
            Fields::CPM         => CPM::make($group)->metric(),
            Fields::CPC         => CPC::make($group)->metric(),
            Fields::CTR         => CTR::make($group)->metric(),
        ];
    }

    /**
     * Get conversion values for report
     *
     * @param \Illuminate\Support\Collection $group
     * @param string                         $name
     *
     * @return array
     */
    protected function conversions($group, $name)
    {
        if ($this->level === Report::LEVEL_CAMPAIGN) {
            return [
                Fields::BCLICKS               => $this->traffic->whereStrict('utm_campaign', $name)->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->whereStrict('utm_campaign', $name)->sum('lp_views') ,
                Fields::LPCLICKS              => $this->traffic->whereStrict('utm_campaign', $name)->sum('lp_clicks'),
                Fields::CRPL                  => sprintf("%s %%", round($this->getLpCtr($name), 2)),
                Fields::CRLP                  => sprintf("%s %%", round($this->getOfferCr($name), 2)),
                Fields::CR                    => sprintf("%s %%", round($this->getCr($name), 2)),
                Fields::LEADS                 => LeadsCount::make($group)->metric(),
                Fields::BLEADS                => $this->traffic->whereStrict('utm_campaign', $name)->sum('leads'),
            ];
        }

        if ($this->level === Report::LEVEL_ACCOUNT) {
            return [
                Fields::BCLICKS               => $this->traffic->whereStrict('accountName', $name)->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->whereStrict('accountName', $name)->sum('lp_views'),
                Fields::LPCLICKS              => $this->traffic->whereStrict('accountName', $name)->sum('lp_clicks'),
                Fields::LEADS                 => LeadsCount::make($group)->metric(),
                Fields::BLEADS                => $this->traffic->whereStrict('accountName', $name)->sum('leads'),
            ];
        }

        if ($this->level === Report::LEVEL_AD) {
            return [
                Fields::BCLICKS               => $this->traffic->whereStrict('utm_content', $name)->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->whereStrict('utm_content', $name)->sum('lp_views') ,
                Fields::LPCLICKS              => $this->traffic->whereStrict('utm_content', $name)->sum('lp_clicks'),
                Fields::CRPL                  => sprintf("%s %%", round($this->getLpCtr($name), 2)),
                Fields::CRLP                  => sprintf("%s %%", round($this->getOfferCr($name), 2)),
                Fields::CR                    => sprintf("%s %%", round($this->getCr($name), 2)),
                Fields::LEADS                 => LeadsCount::make($group)->metric(),
                Fields::BLEADS                => $this->traffic->whereStrict('utm_content', $name)->sum('leads'),
            ];
        }

        if ($this->level === Report::LEVEL_ADSET) {
            return [
                Fields::BCLICKS               => $this->traffic->whereStrict('utm_term', $name)->sum('clicks') ,
                Fields::LPVIEWS               => $this->traffic->whereStrict('utm_term', $name)->sum('lp_views') ,
                Fields::LPCLICKS              => $this->traffic->whereStrict('utm_term', $name)->sum('lp_clicks'),
                Fields::CRPL                  => sprintf("%s %%", round($this->getLpCtr($name), 2)),
                Fields::CRLP                  => sprintf("%s %%", round($this->getOfferCr($name), 2)),
                Fields::CR                    => sprintf("%s %%", round($this->getCr($name), 2)),
                Fields::LEADS                 => LeadsCount::make($group)->metric(),
                Fields::BLEADS                => $this->traffic->whereStrict('utm_term', $name)->sum('leads'),
            ];
        }

        return [
            Fields::LEADS                 => LeadsCount::make($group)->metric(),
        ];
    }

    /**
     * Get CRPL (LP CTR)
     *
     * @param string $name
     *
     * @return float|int
     */
    protected function getLpCtr($name)
    {
        $stats = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $stats = $this->traffic->whereStrict('utm_campaign', $name);
        }
        if ($this->level === Report::LEVEL_AD) {
            $stats = $this->traffic->whereStrict('utm_content', $name);
        }
        if ($this->level === Report::LEVEL_ADSET) {
            $stats = $this->traffic->whereStrict('utm_term', $name);
        }

        if ($stats && $stats->sum('lp_views')) {
            return ($stats->sum('lp_clicks') / $stats->sum('lp_views')) * 100;
        }

        return 0;
    }

    /**
     * Get CRLP (offer CR)
     *
     * @param string $name
     *
     * @return int
     */
    protected function getOfferCr($name)
    {
        $stats = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $stats = $this->traffic->whereStrict('utm_campaign', $name);
        }
        if ($this->level === Report::LEVEL_AD) {
            $stats = $this->traffic->whereStrict('utm_content', $name);
        }
        if ($this->level === Report::LEVEL_ADSET) {
            $stats = $this->traffic->whereStrict('utm_term', $name);
        }

        if ($stats && $stats->sum('lp_clicks')) {
            return ($stats->sum('leads') / $stats->sum('lp_clicks')) * 100;
        }

        return 0;
    }


    /**
     * Get CRLP (offer CR)
     *
     * @param string $name
     *
     * @return int
     */
    protected function getCr($name)
    {
        $stats = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $stats = $this->traffic->whereStrict('utm_campaign', $name);
        }
        if ($this->level === Report::LEVEL_AD) {
            $stats = $this->traffic->whereStrict('utm_content', $name);
        }
        if ($this->level === Report::LEVEL_ADSET) {
            $stats = $this->traffic->whereStrict('utm_term', $name);
        }

        if ($stats && $stats->sum('clicks')) {
            return ($stats->sum('leads') / $stats->sum('clicks')) * 100;
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
     * @param \Illuminate\Support\Collection $group
     * @param string                         $name
     *
     * @return array
     */
    protected function aggregates($group, $name)
    {
        $deposits = $this->deposits->where('name', $name);

        return [
            Fields::FTD                   => $deposits->count(),
            Fields::FTD_PERCENT           => sprintf(
                "%s %%",
                round($this->getLeadsToFtd($group, $deposits->count()), 2)
            ),
            Fields::FTD_COST     => sprintf("\$ %s", round($this->getFtdCost($group, $deposits->count()), 2)),
            Fields::BFTD_PERCENT => sprintf(
                "%s %%",
                round($this->binomLeadsToFtd($name, $deposits->count()), 2)
            ),
            Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue($deposits->count()), 2)),
            Fields::CPL                   => CPL::make($group)->metric(),
            Fields::BCPL                  => $this->getBinomCpl($group, $name),
            Fields::COST                  => Cost::make($group)->metric(),
            Fields::PROFIT                => sprintf(
                "\$ %s",
                round($this->getProfit($group, $deposits->count()), 2)
            ),
            Fields::ROI                   => sprintf(
                "%s %%",
                round($this->getRoi($group, $deposits->count()), 2)
            ),
        ];
    }

    /**
     * Get deposit cost
     *
     * @param $group
     * @param $depositsCount
     *
     * @return float|int
     */
    protected function getFtdCost($group, $depositsCount)
    {
        if ($depositsCount) {
            return Cost::make($group)->value() / $depositsCount;
        }

        return 0;
    }

    /**
     * Get Binom CPL
     *
     * @param mixed $insights
     * @param mixed $key
     *
     * @return float|int|null
     */
    protected function getBinomCpl($insights, $key)
    {
        $leads = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $leads = $this->traffic->whereStrict('utm_campaign', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_ACCOUNT) {
            $leads = $this->traffic->whereStrict('accountName', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_AD) {
            $leads = $this->traffic->whereStrict('utm_content', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_ADSET) {
            $leads = $this->traffic->whereStrict('utm_term', $key)->sum('leads');
        }

        if ($leads) {
            return sprintf("\$ %s", round(Cost::make($insights)->value() / $leads, 2));
        }

        return 0;
    }

    /**
     * Get leads to FTD percentage
     *
     * @param \Illuminate\Support\Collection $group
     * @param int                            $deposits
     *
     * @return false|float|int
     */
    protected function getLeadsToFtd($group, $deposits)
    {
        if ($group->sum(Fields::LEADS) === 0) {
            return 0;
        }

        return ($deposits / $group->sum(Fields::LEADS)) * 100;
    }

    /**
     * Get leads to FTD percentage
     *
     * @param string $key
     * @param int    $deposits
     *
     * @return false|float|int
     */
    protected function binomLeadsToFtd($key, $deposits)
    {
        $leads = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $leads = $this->traffic->whereStrict('utm_campaign', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_ACCOUNT) {
            $leads = $this->traffic->whereStrict('accountName', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_AD) {
            $leads = $this->traffic->whereStrict('utm_content', $key)->sum('leads');
        }

        if ($this->level === Report::LEVEL_ADSET) {
            $leads = $this->traffic->whereStrict('utm_term', $key)->sum('leads');
        }

        if ($leads) {
            return ($deposits / $leads) * 100;
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
     * @param \Illuminate\Support\Collection $group
     * @param $deposits
     *
     * @return string
     */
    protected function getProfit($group, $deposits)
    {
        return $this->getRevenue($deposits) - Cost::make($group)->value();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @param \Illuminate\Support\Collection $group
     * @param int                            $deposits
     *
     * @return string
     */
    protected function getRoi($group, $deposits)
    {
        if (Cost::make($group)->value()) {
            return ($this->getProfit($group, $deposits) / Cost::make($group)->value()) * 100;
        }

        return 0;
    }
}
