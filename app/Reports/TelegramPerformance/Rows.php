<?php

namespace App\Reports\TelegramPerformance;

use App\Binom\Campaign;
use App\Metrics\Telegram\TelegramMetric;
use App\Offer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Rows implements Arrayable
{
    /**
     * Telegram Channel Statistics collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $statistics;

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
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $traffic;

    /**
     * @var null
     */
    protected $offers;

    /**
     * DailyReportSummary constructor.
     *
     * @param Collection                     $statistics
     * @param \Illuminate\Support\Collection $deposits
     * @param string                         $level
     * @param Collection|null                $traffic
     */
    public function __construct(
        $statistics,
        $deposits,
        $level = Report::LEVEL_CAMPAIGN,
        $traffic = null
    ) {
        $this->statistics     =  $statistics;
        $this->deposits       = $deposits;
        $this->level          = $level;
        $this->traffic        = $traffic;
//        $this->offers         = $this->getOffers();
    }

    /**
     * Named constructor
     *
     * @param $statistics
     * @param $deposits
     * @param mixed $level
     * @param null  $traffic
     *
     * @return \App\Reports\TelegramPerformance\Rows
     */
    public static function build(
        $statistics,
        $deposits,
        $level = Report::LEVEL_CAMPAIGN,
        $traffic = null
    ) {
        return new static($statistics, $deposits, $level, $traffic);
    }

    /**
     * Get array representation of rows
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function toArray()
    {
        return $this->statistics
            ->groupBy('name')
            ->map(function (Collection $group, $name) {
                return array_merge(
                    $this->defaults($group, $name),
                    $this->conversions($group, $name),
                    $this->aggregates($group, $name),
                );
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
        $metric = new TelegramMetric($this->traffic->whereStrict('utm_campaign', $name), $group);

//        $offer = $this->offers->get(optional($this->traffic->whereStrict('utm_campaign', $name)->first())->campaign_id);

        return [
            Fields::NAME        => $name ? $name : 'Unknown',
            Fields::OFFER       => 'GAZPROM',
            Fields::RK_COUNT    => $group->unique('channel_id')->count(),
            Fields::IMPRESSIONS => $metric->impressions(),

            Fields::CLICKS      => $metric->clicks(),
            Fields::CPM         => $metric->getCpm(),
            Fields::CPC         => $metric->getCpc(),
            Fields::CTR         => $metric->getCtr(),
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
        $metric = new TelegramMetric($this->traffic->whereStrict('utm_campaign', $name), $group);

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            return [
                Fields::BCLICKS             => $metric->bclicks(),
                Fields::LPVIEWS             => $metric->lpViews(),
                Fields::LPCLICKS            => $metric->lpClicks(),
                Fields::CRPL                => $metric->getLpCtr(),
                Fields::CRLP                => $metric->getOfferCr(),
                Fields::CR                  => $metric->getCr(),
                Fields::BLEADS              => $metric->leads(),
            ];
        }

        return [
            Fields::BLEADS              => $metric->leads(),
        ];
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
        $deposits_count = $this->deposits->where('name', strtolower($name))->count();
        $metric         = new TelegramMetric($this->traffic->whereStrict('utm_campaign', $name), $group);

        if (in_array($this->level, [Report::LEVEL_CAMPAIGN])) {
            return [
                Fields::FTD                   => $deposits_count,
                Fields::BFTD_PERCENT          => sprintf(
                    "%s %%",
                    round($this->binomLeadsToFtd($name, $deposits_count), 2)
                ),
                Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue($deposits_count), 2)),
                Fields::BCPL                  => $this->getBinomCpl($group, $name),
                Fields::COST                  => $metric->cost(),
                Fields::PROFIT                => sprintf(
                    "\$ %s",
                    round($this->getProfit($group, $deposits_count), 2)
                ),
                Fields::ROI                   => sprintf(
                    "%s %%",
                    round($this->getRoi($group, $deposits_count), 2)
                ),
            ];
        }

        return [];
    }

    /**
     * Get offers
     *
     * @return Offer[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getOffers()
    {
        $campaigns = Campaign::query()
            ->whereIn('id', $this->traffic->pluck('campaign_id')->unique())
            ->get(['id', 'offer_id'])
            ->mapWithKeys(function ($campaign) {
                return [$campaign->offer_id => $campaign->id];
            });

        return Offer::query()
            ->whereIn('id', $campaigns->keys())
            ->get(['id', 'name'])
            ->mapWithKeys(function ($offer) use ($campaigns) {
                return [$campaigns->get($offer->id) => $offer];
            });
    }

    /**
     * Get Binom CPL
     *
     * @param mixed $group
     * @param mixed $key
     *
     * @return float|int|null
     */
    protected function getBinomCpl($group, $key)
    {
        $leads = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $leads = $this->traffic->whereStrict('utm_campaign', $key)->sum('leads');
        }

        if ($leads) {
            return sprintf("\$ %s", round($group->sum('cost') / $leads, 2));
        }

        return 0;
    }

    /**
     * Get leads to FTD percentage
     *
     * @param string $name
     * @param int    $deposits
     *
     * @return false|float|int
     */
    protected function binomLeadsToFtd($name, $deposits)
    {
        $leads = null;

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            $leads = $this->traffic->whereStrict('utm_campaign', $name)->sum('leads');
        }
        if ($leads) {
            return $deposits / $leads * 100;
        }

        return 0;
    }

    /**
     * Get revenue
     * Depends on office model.
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
        return $this->getRevenue($deposits) - $group->sum('cost');
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
        if ($group->sum('cost')) {
            return ($this->getProfit($group, $deposits) / $group->sum('cost')) * 100;
        }

        return 0;
    }
}
