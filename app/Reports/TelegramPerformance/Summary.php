<?php

namespace App\Reports\TelegramPerformance;

use App\Metrics\Telegram\TelegramMetric;
use Illuminate\Contracts\Support\Arrayable;
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
     * Telegram Channel Statistics collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $statistics;

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
     * @var int
     */
    protected $deposits_count;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $statistics
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     */
    public function __construct(Collection $statistics, Collection $deposits, $level, $traffic = null)
    {
        $this->statistics     = $statistics;
        $this->deposits       = $deposits;
        $this->level          = $level;
        $this->traffic        = $traffic;
        $this->deposits_count = $this->deposits->count();
    }

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $statistics
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     *
     * @return \App\Reports\TelegramPerformance\Summary
     */
    public static function build(Collection $statistics, Collection $deposits, $level, $traffic = null)
    {
        return new self($statistics, $deposits, $level, $traffic);
    }

    /**
     * Get array representation of summary
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->defaults(),
            $this->conversions(),
            $this->aggregates(),
        );
    }

    /**
     * Get default values for report
     *
     * @return array
     */
    protected function defaults(): array
    {
        $metric = new TelegramMetric($this->traffic, $this->statistics);

        return [
            Fields::NAME                  => 'ИТОГО',
            Fields::OFFER                 => '',
            Fields::RK_COUNT              => $this->statistics->unique('channel_id')->count(),
            Fields::IMPRESSIONS           => $metric->impressions(),
            Fields::CLICKS                => $metric->clicks(),
            Fields::CPM                   => $metric->getCpm(),
            Fields::CPC                   => $metric->getCpc(),
            Fields::CTR                   => $metric->getCtr(),
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
        $metric = new TelegramMetric($this->traffic, $this->statistics);

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            return [
                Fields::BCLICKS               => $metric->bclicks(),
                Fields::LPVIEWS               => $metric->lpViews(),
                Fields::LPCLICKS              => $metric->lpClicks(),
                Fields::CRPL                  => $metric->getLpCtr(),
                Fields::CRLP                  => $metric->getOfferCr(),
                Fields::CR                    => $metric->getCr(),
                Fields::BLEADS                => $metric->leads(),
            ];
        }

        return [
            Fields::BLEADS                => $metric->leads(),
        ];
    }

    /**
     * Get aggregate values for report
     *
     *
     * @return array
     */
    protected function aggregates(): array
    {
        $metric = new TelegramMetric($this->traffic, $this->statistics);

        if (in_array($this->level, [Report::LEVEL_CAMPAIGN])) {
            return [
                Fields::FTD                   => $this->deposits_count,
                Fields::BFTD_PERCENT          => sprintf(
                    "%s %%",
                    round($this->binomLeadsToFtd(), 2)
                ),
                Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue(), 2)),
                Fields::BCPL                  => $this->getBinomCpl(),
                Fields::COST                  => $metric->cost(),
                Fields::PROFIT                => sprintf("\$ %s", round($this->getProfit(), 2)),
                Fields::ROI                   => sprintf("%s %%", round($this->getRoi(), 2)),
            ];
        }

        return [];
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
            return ($this->deposits_count / $leads) * 100;
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
        $leads = $this->traffic->sum('leads');

        if ($leads) {
            return sprintf("\$ %s", round($this->statistics->sum('cost') / $leads, 2));
        }

        return 0;
    }

    /**
     * Get revenue
     * Depends on office model.
     *
     * @return string
     */
    protected function getRevenue()
    {
        return $this->deposits_count * 400;
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return string
     */
    protected function getProfit()
    {
        return $this->getRevenue() - $this->statistics->sum('cost');
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return string
     */
    protected function getRoi()
    {
        if (($costs = $this->statistics->sum('cost'))) {
            return ($this->getProfit() / $costs) * 100;
        }

        return 0;
    }
}
