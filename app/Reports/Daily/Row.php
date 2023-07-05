<?php

namespace App\Reports\Daily;

use App\Deposit;
use App\Facebook\Account;
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
use App\Offer;
use App\Office;
use App\Result;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class Row implements Arrayable
{
    /**
     * Effective report row date
     *
     * @var \Carbon\Carbon
     */
    protected $date;

    /**
     * Offer filter
     *
     * @var \App\Offer
     */
    protected $offer;

    /**
     * Office filter
     *
     * @var \App\Office
     */
    protected $office;

    /**
     * Results collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $results;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Determines simple of full version of report should be loaded
     *
     * @var bool
     */
    protected $simple;

    /**
     *
     * @var array|null
     */
    protected $users;

    /**
     *
     * @var float
     */
    protected $bcpl;

    /**
     * DailyReportRow constructor.
     *
     * @param \Carbon\Carbon   $date
     * @param \App\Offer       $offer
     * @param bool             $simple
     * @param \App\Office|null $office
     * @param mixed            $users
     *
     * @return void
     */
    public function __construct(Carbon $date, Offer $offer, $simple = false, ?Office $office = null, $users = null)
    {
        $this->date   = $date;
        $this->offer  = $offer;
        $this->office = $office;
        $this->simple = $simple;
        $this->users  = $users;
    }

    /**
     * Get array representation of the row
     *
     * @return array
     */
    public function toArray()
    {
        return $this->isSimple() ? $this->simple() : $this->full();
    }

    /**
     * Format data for buyer
     *
     * @return array
     */
    protected function simple()
    {
        $this->fetch();

        return [
            Fields::DATE                  => $this->date->toDateString(),
            Fields::OFFER                 => $this->offer->name,
            Fields::CLICKS                => Clicks::make($this->insights)->metric(),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
            Fields::CR                    => ConversionRate::make()
                ->forDate($this->date)
                ->forOffers($this->offer)
                ->forUsers($this->users)
                ->metric(),
            Fields::LEADS                => $this->getLeads(),
            Fields::BLEADS               => Leads::make()
                ->forDate($this->date)
                ->forOffers($this->offer)
                ->forUsers($this->users)
                ->metric(),
            Fields::FTD                  => $this->getFtd(),
            Fields::FTD_PERCENT          => sprintf("%s %%", round($this->leadsTo($this->getFtd()), 2)),
            Fields::REVENUE              => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL                  => CPL::make($this->insights)->metric(),
            Fields::BCPL                 => sprintf("\$ %s", round($this->bcpl, 2)),
            Fields::COST                 => Cost::make($this->insights)->metric(),
            Fields::PROFIT               => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                  => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    /**
     * Format data for admin
     *
     * @return array
     */
    protected function full()
    {
        $this->fetch();

        return [
            Fields::DATE                 => $this->date->toDateString(),
            Fields::DESK                 => $this->office->name,
            Fields::OFFER                => $this->offer->name,
            Fields::CLICKS               => round($this->getClicks(), 2),
            Fields::CPM                  => CPM::make($this->insights)->metric(),
            Fields::CPC                  => CPC::make($this->insights)->metric(),
            Fields::CTR                  => CTR::make($this->insights)->metric(),
            Fields::CRPL                 => PrelandingConversion::make()->forDate($this->date)
                ->forOffers($this->offer)->metric(),
            Fields::CRLP                 => LandingConversion::make()->forDate($this->date)
                ->forOffers($this->offer)->metric(),
            Fields::CR => ConversionRate::make()->forDate($this->date)
                ->forOffers($this->offer)->metric(),
            Fields::LEADS                => $this->getLeads(),
            Fields::FTD                  => $this->getFtd(),
            Fields::FTD_PERCENT          => sprintf("%s %%", round($this->leadsTo($this->getFtd()), 2)),
            Fields::REVENUE              => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::BCPL                 => sprintf("\$ %s", round($this->bcpl, 2)),
            Fields::BCOST                => sprintf("\$ %s", round($this->getBCost(), 2)),
            Fields::PROFIT               => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                  => sprintf("%s %%", round($this->getBRoi(), 2)),
        ];
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
     * Get clicks attribute
     *
     * @return false|float|int
     */
    protected function getClicks()
    {
        if ($cpc = CPC::make($this->insights)->value()) {
            return $this->getCosts() / $cpc;
        }

        return 0;
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
            return $this->getRevenue() - $this->getCosts();
        }

        return $this->getRevenue() - $this->getBCost();
    }

    /**
     * Calculate costs
     *
     * @return int|float
     */
    protected function getCosts()
    {
        return CPL::make($this->insights)->value() * $this->getLeads();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return int|float
     */
    protected function getBRoi()
    {
        if ($this->getBCost()) {
            return
                ($this->getProfit() / $this->getBCost()) * 100;
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
        if ($this->getCosts()) {
            return
                ($this->getProfit() / $this->getCosts()) * 100;
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
            return $number / $this->getLeads() * 100;
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
            ->where([
                'lead_return_date' => $this->date->toDateString(),
                'offer_id'         => $this->offer->id,
            ])
            ->when($this->office, function (Builder $builder) {
                return $builder->where('office_id', $this->office->id);
            })
            ->when($this->users, function (Builder $builder) {
                return $builder->whereIn('user_id', $this->users);
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
            return ($this->getFtd() / $this->results->sum(Fields::DEMO)) * 100;
        }

        return 0;
    }

    /**
     * Fetch results deposits and insights
     *
     * @return void
     */
    protected function fetch()
    {
        $this->fetchInsights();
        $this->fetchResults();
        $this->setBCpl();
    }

    /**
     * Load results from database
     *
     * @return \App\Reports\Daily\Row
     */
    protected function fetchResults()
    {
        $this->results = $this->isSimple()
            ? collect()
            : Result::where([
                'date'      => $this->date->toDateString(),
                'offer_id'  => $this->offer->id,
                'office_id' => $this->office->id,
            ])->get();

        return $this;
    }

    /**
     * Load insights from database
     *
     * @return \App\Reports\Daily\Row
     */
    protected function fetchInsights()
    {
        $this->insights = Insights::visible()
            ->where([
                'date'       => $this->date->toDateString(),
                'offer_id'   => $this->offer->id,
            ])
            ->when($this->users, function (Builder $query) {
                return $query->whereIn('account_id', Account::forUsers($this->users)->pluck('account_id'));
            })
            ->notEmptyWhereIn('user_id', $this->users)
            ->get();

        return $this;
    }

    /**
     * preset bcpl
     *
     * @return \App\Reports\Daily\Row
     */
    protected function setBCpl()
    {
        $this->bcpl = \App\Metrics\Binom\CPL::make()
            ->useCosts(Cost::make($this->insights)->value())
            ->forDate($this->date)
            ->forOffers($this->offer)
            ->forUsers($this->users)
            ->value();

        return $this;
    }

    /**
     * Determines what type of report we are building
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
