<?php

namespace App\Reports\Daily;

use App\Deposit;
use App\Insights;
use App\Metrics\Binom\ConversionRate;
use App\Metrics\Binom\LandingConversion;
use App\Metrics\Binom\Leads;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\LeadsCount;
use App\Metrics\PrelandingConversion;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class Summary implements Arrayable
{
    /**
     * Start date for the report
     *
     * @var \Carbon\Carbon
     */
    protected $since;

    /**
     * End date for the report
     *
     * @var \Carbon\Carbon
     */
    protected $until;

    /**
     * Offices for report
     *
     * @var \Illuminate\Support\Collection
     */
    protected $offices;

    /**
     * Offers for report
     *
     * @var \Illuminate\Support\Collection
     */
    protected $offers;

    /**
     * Insights cached from the Facebook
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Results collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $results;

    /**
     * Simplified version of the report
     *
     * @var bool
     */
    protected $simple = false;

    /**
     * @var null
     */
    protected $users = null;

    /**
     * @var float
     */
    protected $bcpl;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Carbon\Carbon                           $since
     * @param \Carbon\Carbon                           $until
     * @param \Illuminate\Database\Eloquent\Collection $offices
     * @param \Illuminate\Database\Eloquent\Collection $offers
     * @param bool                                     $simple
     * @param null|mixed                               $users
     * @param mixed                                    $results
     * @param mixed                                    $insights
     */
    public function __construct($since, $until, $offices, $offers, $simple, $users = null, $results = [], $insights = [])
    {
        $this->since    = $since;
        $this->until    = $until;
        $this->offices  = $offices;
        $this->offers   = $offers;
        $this->simple   = $simple;
        $this->users    = $users;
        $this->results  = $results;
        $this->insights = $insights;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->isSimple() ? $this->simple() : $this->full();
    }

    /**
     * Named constructor
     *
     * @param \Carbon\Carbon                           $since
     * @param \Carbon\Carbon                           $until
     * @param \Illuminate\Database\Eloquent\Collection $offices
     * @param \Illuminate\Database\Eloquent\Collection $offers
     * @param mixed                                    $simple
     * @param mixed                                    $users
     * @param mixed                                    $results
     * @param mixed                                    $insights
     *
     * @return \App\Reports\Daily\Summary
     */
    public static function build($since, $until, $offices, $offers, $simple, $users = null, $results = [], $insights = [])
    {
        return new static($since, $until, $offices, $offers, $simple, $users, $results, $insights);
    }

    /**
     * Simple version of report
     *
     * @return array
     */
    protected function simple()
    {
        return [
            Fields::DATE                  => 'ИТОГО',
            Fields::OFFER                 => null,
            Fields::CLICKS                => Clicks::make($this->insights)->metric(),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
            Fields::CR                    => ConversionRate::make()->since($this->since)->until($this->until)
                ->forOffers($this->offers)->forUsers($this->users)->metric(),
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::BLEADS                => Leads::make()->since($this->since)->until($this->until)
                ->forOffers($this->offers)->forUsers($this->users)->metric(),
            Fields::FTD                   => $this->getFtd(),
            Fields::FTD_PERCENT           => sprintf("%s %%", $this->leadsTo($this->getFtd())),
            Fields::REVENUE               => sprintf("\$ %s", $this->getRevenue()),
            Fields::CPL                   => CPL::make($this->insights)->metric(),
            Fields::BCPL                  => \App\Metrics\Binom\CPL::make()
                ->useCosts(Cost::make($this->insights)->value())
                ->since($this->since)
                ->until($this->until)
                ->forOffers($this->offers)
                ->forUsers($this->users)
                ->metric(),
            Fields::COST                  => Cost::make($this->insights)->metric(),
            Fields::PROFIT                => sprintf("\$ %s", $this->getProfit()),
            Fields::ROI                   => sprintf("%s %%", $this->getRoi()),
        ];
    }

    /**
     * Full version of report
     *
     * @return array
     */
    protected function full()
    {
        $this->bcpl = \App\Metrics\Binom\CPL::make()
            ->useCosts(Cost::make($this->insights)->value())
            ->since($this->since)
            ->until($this->until)
            ->forOffers($this->offers)
            ->value();

        return  [
            Fields::DATE                  => "ИТОГО",
            Fields::DESK                  => null,
            Fields::OFFER                 => null,
            Fields::CLICKS                => round($this->getClicks(), 2),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
            Fields::CRPL                  => PrelandingConversion::make()->since($this->since)->until($this->until)->forOffers($this->offers)->metric(),
            Fields::CRLP                  => LandingConversion::make()->since($this->since)->until($this->until)->forOffers($this->offers)->metric(),
            Fields::CR                    => ConversionRate::make()->since($this->since)->until($this->until)
                ->forOffers($this->offers)->metric(),
            Fields::LEADS                 => $this->getLeads(),
            Fields::FTD                   => $this->getFtd(),
            Fields::FTD_PERCENT           => sprintf("%s %%", $this->leadsTo($this->getFtd())),
            Fields::REVENUE               => sprintf("\$ %s", $this->getRevenue()),
            Fields::BCPL                  => sprintf("\$ %s", round($this->bcpl, 2)),
            Fields::BCOST                 => sprintf("\$ %s", round($this->getBCost(), 2)),
            Fields::PROFIT                => sprintf("\$ %s", $this->getProfit()),
            Fields::ROI                   => sprintf("%s %%", $this->getBRoi()),
        ];
    }

    /**
     * Get clicks attribute
     *
     * @return false|float|int
     */
    protected function getClicks()
    {
        if ($cpc = CPC::make($this->insights)->value()) {
            return $this->getCost() / $cpc;
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
        if ($this->isSimple()) {
            return LeadsCount::make($this->insights)->value();
        }

        return $this->results->sum(Fields::LEADS);
    }

    /**
     * Get revenue
     *
     * @return int|float
     */
    protected function getRevenue()
    {
        return $this->getFtd() * 400;
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return int|float
     */
    protected function getProfit()
    {
        if ($this->isSimple()) {
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
     * Percentage of all leads to some status
     *
     * @param int|float $number
     *
     * @return int|float
     */
    protected function leadsTo($number = 0)
    {
        if ($this->getLeads()) {
            return round($number / $this->getLeads() * 100, 2);
        }

        return 0;
    }

    /**
     * Get count of deposits
     *
     * @return int
     */
    public function getFtd()
    {
        return Deposit::visible()
            ->allowedOffers()
            ->whereBetween('lead_return_date', [$this->since->toDateString(), $this->until->toDateString()])
            ->where(function ($query) {
                $query->whereIn('offer_id', $this->offers->pluck('id')->values())
                    ->orWhereNull('offer_id');
            })
            ->whereIn('office_id', $this->offices->pluck('id')->values())
            ->when($this->users, function (Builder $query) {
                return $query->whereIn('user_id', $this->users);
            })->count();
    }

    /**
     * Get demo to FTD percentage
     *
     * @return float|int
     */
    protected function getDemoToFtd()
    {
        if ($this->results->sum(Fields::DEMO)) {
            return round((
                $this->getFtd() / $this->results->sum(Fields::DEMO)
            ) * 100, 2);
        }

        return 0;
    }

    /**
     * Determines what report we are building
     *
     * @return bool
     */
    protected function isSimple()
    {
        return auth()->user()->isBuyer() || $this->simple;
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
}
