<?php

namespace App\Reports\AccountsDaily;

use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use App\Metrics\Binom\LandingConversion;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\LeadsCount;
use App\Metrics\PrelandingConversion;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class Row implements Arrayable
{
    /**
     * Effective report row date
     *
     * @var \Carbon\Carbon
     */
    protected $date;

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
     * Ads account
     *
     * @var \App\Facebook\Account
     */
    protected $account;

    /**
     * DailyReportRow constructor.
     *
     * @param \Carbon\Carbon        $date
     * @param \App\Facebook\Account $account
     */
    public function __construct(Carbon $date, Account $account)
    {
        $this->date    = $date;
        $this->account = $account;
    }

    /**
     * Get formatted report row
     *
     * @return array
     */
    protected function row()
    {
        $this->fetchData();

        return [
            Fields::DATE                 => $this->date->toDateString(),
            Fields::ACCOUNT              => $this->account->name,
            Fields::IMPRESSIONS          => $this->insights->sum(Fields::IMPRESSIONS),
            Fields::CLICKS               => Clicks::make($this->insights)->metric(),
            Fields::CPM                  => CPM::make($this->insights)->metric(),
            Fields::CPC                  => CPC::make($this->insights)->metric(),
            Fields::CTR                  => CTR::make($this->insights)->metric(),
            Fields::CRPL                 => PrelandingConversion::make()->forDate($this->date)->metric(),
            Fields::CRLP                 => LandingConversion::make()->forDate($this->date)->metric(),
            Fields::LEADS                => LeadsCount::make($this->insights)->metric(),
            Fields::FTD                  => $this->deposits,
            Fields::FTD_PERCENT          => sprintf("%s %%", round($this->getLeadsToFtd(), 2)),
            Fields::REVENUE              => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL                  => CPL::make($this->insights)->metric(),
            Fields::COST                 => Cost::make($this->insights)->metric(),
            Fields::PROFIT               => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                  => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    /**
     * Get array representation of the row
     *
     * @return array
     */
    public function toArray()
    {
        return $this->row();
    }

    /**
     * Get leads to FTD percentage
     *
     * @return false|float|int
     */
    protected function getLeadsToFtd()
    {
        if (LeadsCount::make($this->insights)->value() === 0) {
            return 0;
        }

        return ($this->deposits / LeadsCount::make($this->insights)->value()) * 100;
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
        if (Cost::make($this->insights)->value()) {
            return ($this->getProfit() / Cost::make($this->insights)->value()) * 100;
        }

        return 0;
    }

    /**
     * Fetch results deposits and insights
     *
     * @return void
     */
    protected function fetchData()
    {
        $this->fetchInsights();
        $this->fetchDeposits();
    }

    /**
     * Load results from database
     *
     * @return $this
     */
    protected function fetchDeposits()
    {
        $this->deposits  = Deposit::visible()
            ->allowedOffers()
            ->whereDate('lead_return_date', $this->date->toDateString())
            ->where('account_id', $this->account->account_id)
            ->count();

        return $this;
    }

    /**
     * Load insights from database
     *
     * @return $this
     */
    protected function fetchInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->whereDate('date', $this->date->toDateString())
            ->where('account_id', $this->account->account_id)
            ->get();

        return $this;
    }
}
