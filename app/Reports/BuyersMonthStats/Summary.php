<?php

namespace App\Reports\BuyersMonthStats;

use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\LeadsCount;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
     * Insights cached from the Facebook
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Deposits collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $deposits;

    /**
     * @var Collection
     */
    protected $users;

    /**
     * BuyersMonthReportSummary constructor.
     *
     * @param mixed          $users
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     */
    public function __construct($users, Carbon $since, Carbon $until)
    {
        $this->users    = $users;
        $this->since    = $since;
        $this->until    = $until;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $this->fetchData();

        return [
            Fields::BUYER                 => 'ИТОГО',
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::FTD                   => $this->getFtd()
                                                    . '/' .
                                                    sprintf("%s %%", $this->leadsTo($this->getFtd())),
            Fields::CPL                   => CPL::make($this->insights)->metric(),
            Fields::COST                  => Cost::make($this->insights)->metric(),
            Fields::PROFIT                => sprintf("\$ %s", $this->getProfit()),
            Fields::ROI                   => sprintf("%s %%", $this->getRoi()),
        ];
    }

    /**
     * Named constructor
     *
     * @param mixed          $users
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return Summary
     */
    public static function build($users, $since, $until)
    {
        return new static($users, $since, $until);
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
        return LeadsCount::make($this->insights)->value();
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
        return round($this->getRevenue() - $this->getCost(), 2);
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
        return $this->deposits->count();
    }

    /**
     * Fetch deposits and insights
     *
     * @return void
     */
    protected function fetchData()
    {
        $this->fetchInsights();
        $this->fetchDeposits();
    }

    /**
     * Load deposits from database
     *
     * @return Summary
     */
    protected function fetchDeposits()
    {
        $this->deposits = Deposit::allowedOffers()
            ->whereBetween('lead_return_date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->users->isNotEmpty(), function (Builder $query) {
                return $query->whereIn('user_id', $this->users->pluck('id')->values());
            })
            ->get();

        return $this;
    }

    /**
     * Load insights from database
     *
     * @return Summary
     */
    protected function fetchInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->users->isNotEmpty(), function (Builder $query) {
                return $query->whereIn(
                    'account_id',
                    Account::forUsers($this->users->pluck('id')->toArray())->pluck('account_id')
                );
            })
            ->when($this->users, fn ($q) => $q->whereIn('user_id', $this->users->pluck('id')))
            ->get();

        return $this;
    }
}
