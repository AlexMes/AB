<?php

namespace App\Reports\Daily;

use App\Metrics\Binom\Leads;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\LeadsCount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Late
{
    /**
     * @var bool
     */
    protected $isSimple;
    /**
     * @var Collection
     */
    protected $deposits;
    /**
     * @var int
     */
    protected $leads;
    /**
     * @var int
     */
    protected $depositsForPeriod;
    /**
     * @var int
     */
    protected $depositsForMonth;
    /**
     * @var Carbon
     */
    protected $since;
    /**
     * @var Carbon
     */
    protected $until;
    /**
     * @var float
     */
    protected $bcpl;
    /**
     * @var Collection
     */
    protected $insights;
    /**
     * @var Collection
     */
    protected $offers;

    /**
     * Late constructor.
     *
     * @param Collection $insights
     * @param Collection $offers
     * @param Collection $deposits
     * @param int        $leads
     * @param $since
     * @param $until
     * @param bool $isSimple
     */
    public function __construct($insights, $offers, $deposits, $leads, $since, $until, $isSimple = false)
    {
        $this->deposits = $deposits;
        $this->leads    = $leads;
        $this->isSimple = $isSimple;
        $this->since    = $since;
        $this->until    = $until;
        $this->offers   = $offers;
        $this->insights = $insights;
    }

    /**
     * @param Collection $insights
     * @param Collection $offers
     * @param Collection $deposits
     * @param int        $leads
     * @param $since
     * @param $until
     * @param bool $isSimple
     *
     * @return Late
     */
    public static function build($insights, $offers, $deposits, $leads, $since, $until, $isSimple = false)
    {
        return (new self($insights, $offers, $deposits, $leads, $since, $until, $isSimple))
            ->guessPeriodDeposits()
            ->guessMonthDeposits()
            ->guessBcpl();
    }

    /**
     * Get styled stats for specific report
     *
     * @return array
     */
    public function returnedMetric()
    {
        return $this->isSimple ?
            $this->getPeriodSimpleStats() :
            $this->getPeriodFullStats();
    }

    /**
     * Get styled stats for specific report
     *
     * @return array
     */
    public function lateMetric()
    {
        return $this->isSimple ?
            $this->getLateSimpleStats() :
            $this->getLateFullStats();
    }

    /**
     * Get simple report with pretty cells
     *
     * @return array
     */
    public function getPeriodSimpleStats()
    {
        return [
            Fields::DATE                  => 'Депозиты за <br> месяц',
            Fields::OFFER                 => null,
            Fields::CLICKS                => null,
            Fields::CPM                   => null,
            Fields::CPC                   => null,
            Fields::CTR                   => null,
            Fields::CR                    => null,
            Fields::LEADS                 => null,
            Fields::BLEADS                => null,
            Fields::FTD                   => $this->depositsForPeriod,
            Fields::FTD_PERCENT           => sprintf("%s %%", $this->getReturnedFtdPercent()),
            Fields::REVENUE               => sprintf("\$ %s", $this->getRevenue()),
            Fields::CPL                   => CPL::make($this->insights)->metric(),
            Fields::BCPL                  => sprintf("\$ %s", round($this->bcpl, 2)),
            Fields::COST                  => Cost::make($this->insights)->metric(),
            Fields::PROFIT                => sprintf("\$ %s", $this->getProfit()),
            Fields::ROI                   => sprintf("%s %%", $this->getRoi()),
        ];
    }

    /**
     * Get full report with pretty cells
     *
     * @return array
     */
    public function getPeriodFullStats()
    {
        return [
            Fields::DATE                 => 'Депозиты за <br> месяц',
            Fields::DESK                 => null,
            Fields::OFFER                => null,
            Fields::CLICKS               => null,
            Fields::CPM                  => null,
            Fields::CPC                  => null,
            Fields::CTR                  => null,
            Fields::CRPL                 => null,
            Fields::CRLP                 => null,
            Fields::CR                   => null,
            Fields::LEADS                => null,
            Fields::FTD                  => $this->depositsForPeriod,
            Fields::FTD_PERCENT          => sprintf("%s %%", $this->getReturnedFtdPercent()),
            Fields::REVENUE              => sprintf("\$ %s", $this->getRevenue()),
            Fields::BCPL                 => sprintf("\$ %s", round($this->bcpl, 2)),
            Fields::BCOST                => sprintf("\$ %s", round($this->getBCost(), 2)),
            Fields::PROFIT               => sprintf("\$ %s", $this->getProfit()),
            Fields::ROI                  => sprintf("%s %%", $this->getBRoi()),
        ];
    }

    /**
     * @return array
     */
    public function getLateSimpleStats()
    {
        return [
            Fields::DATE                  => 'Долёты',
            Fields::OFFER                 => null,
            Fields::CLICKS                => null,
            Fields::CPM                   => null,
            Fields::CPC                   => null,
            Fields::CTR                   => null,
            Fields::CR                    => null,
            Fields::LEADS                 => null,
            Fields::BLEADS                => null,
            Fields::FTD                   => $this->getLateDeposits() . ' / ' . sprintf("%s %%", $this->getLatePercent()),
            Fields::FTD_PERCENT           => sprintf("%s %%", $this->getLateFtdPercent()),
            Fields::REVENUE               => null,
            Fields::CPL                   => null,
            Fields::BCPL                  => null,
            Fields::COST                  => null,
            Fields::PROFIT                => null,
            Fields::ROI                   => null,
        ];
    }

    /**
     * Get raw stats
     *
     * @return array
     */
    public function getLateFullStats()
    {
        return [
            Fields::DATE                  => 'Долёты',
            Fields::DESK                  => null,
            Fields::OFFER                 => null,
            Fields::CLICKS                => null,
            Fields::CPM                   => null,
            Fields::CPC                   => null,
            Fields::CTR                   => null,
            Fields::CRPL                  => null,
            Fields::CRLP                  => null,
            Fields::CR                    => null,
            Fields::LEADS                 => null,
            Fields::FTD                   => $this->getLateDeposits() . ' / ' . sprintf("%s %%", $this->getLatePercent()),
            Fields::FTD_PERCENT           => sprintf("%s %%", $this->getLateFtdPercent()),
            Fields::REVENUE               => null,
            Fields::BCPL                  => null,
            Fields::BCOST                 => null,
            Fields::PROFIT                => null,
            Fields::ROI                   => null,
        ];
    }

    /**
     * Get revenue
     *
     * @return int|float
     */
    protected function getRevenue()
    {
        return $this->depositsForPeriod * 400;
    }

    /**
     * @return false|float|int
     */
    protected function getReturnedFtdPercent()
    {
        if ($this->leads > 0) {
            return round(($this->depositsForPeriod / $this->leads) * 100, 2);
        }

        return 0;
    }

    /**
     * @return false|float|int
     */
    protected function getLateFtdPercent()
    {
        $all = $this->getAllDeposits();

        if ($all > 0) {
            return round(($this->getLateDeposits() / $all) * 100, 2);
        }

        return 0;
    }

    /**
     * @return false|float|int
     */
    protected function getLatePercent()
    {
        if ($this->leads > 0) {
            return round(($this->getLateDeposits() / $this->leads) * 100, 2);
        }

        return 0;
    }

    /**
     * @return mixed
     */
    protected function getAllDeposits()
    {
        return $this->deposits->count();
    }

    /**
     * Calculate late deposits for period
     *
     * @return int
     */
    protected function getLateDeposits()
    {
        return $this->getAllDeposits() - $this->depositsForMonth;
    }

    /**
     * Deposits for month
     *
     * @return Late
     */
    protected function guessMonthDeposits()
    {
        $this->depositsForMonth = $this->deposits
            ->whereBetween('date', [
                $this->since->copy()->startOfMonth()->toDateString(),
                $this->since->copy()->endOfMonth()->toDateString()
            ])->count();

        return $this;
    }

    /**
     * Deposits for period
     *
     * @return Late
     */
    protected function guessPeriodDeposits()
    {
        $this->depositsForPeriod = $this->deposits
            ->whereBetween('date', [
                $this->since->toDateString(),
                $this->until->toDateString()
            ])->count();

        return $this;
    }

    /**
     * calculate binom cpl
     *
     * @return $this
     */
    protected function guessBcpl()
    {
        $this->bcpl = \App\Metrics\Binom\CPL::make()
            ->useCosts(Cost::make($this->insights)->value())
            ->since($this->since)
            ->until($this->until)
            ->forOffers($this->offers)
            ->value();

        return $this;
    }

    /**
     * Calculate binom leads cost
     *
     * @return float
     */
    protected function getBCost()
    {
        return $this->bcpl * $this->getLeads();
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return int|float
     */
    protected function getProfit()
    {
        if ($this->isSimple) {
            return round($this->getRevenue() - $this->getCost(), 2);
        }

        return round($this->getRevenue() - $this->getBCost(), 2);
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return int|float
     */
    protected function getBRoi()
    {
        if ($this->getBCost()) {
            return round(
                ($this->getProfit() / $this->getBCost()) * 100,
                2
            );
        }

        return 0;
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return int|float
     */
    protected function getRoi()
    {
        if ($this->getCost()) {
            return round(
                ($this->getProfit() / $this->getCost()) * 100,
                2
            );
        }

        return 0;
    }

    /**
     * Get costs
     *
     * @return false|float
     */
    protected function getCost()
    {
        return CPL::make($this->insights)->value() * $this->getLeads();
    }

    /**
     * Get leads count
     *
     * @return mixed
     */
    protected function getLeads()
    {
        if ($this->isSimple) {
            return LeadsCount::make($this->insights)->value();
        }

        return $this->leads;
    }
}
