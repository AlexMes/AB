<?php

namespace App\Reports\BuyersMonthStats;

use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\LeadsCount;
use App\User;
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
    protected $since;

    /**
     * Effective report row date
     *
     * @var \Carbon\Carbon
     */
    protected $until;

    /**
     * Deposits collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $deposits;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     *
     * @var User
     */
    protected $user;

    /**
     * BuyerMonthReportRow constructor.
     *
     * @param User   $user
     * @param Carbon $since
     * @param Carbon $until
     */
    public function __construct(User $user, Carbon $since, Carbon $until)
    {
        $this->user    = $user;
        $this->since   = $since;
        $this->until   = $until;
    }

    /**
     * Get array representation of the row
     *
     * @return array
     */
    public function toArray()
    {
        $this->fetch();

        return [
            Fields::BUYER                  => $this->user->name,
            Fields::LEADS                  => $this->getLeads(),
            Fields::FTD                    => $this->getFtd() .
                                              '/'
                                              . sprintf("%s %%", round($this->leadsTo($this->getFtd()), 2)),
            Fields::CPL                    => CPL::make($this->insights)->metric(),
            Fields::COST                   => Cost::make($this->insights)->metric(),
            Fields::PROFIT                 => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                    => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
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
        return $this->getRevenue() - $this->getCosts();
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
        return $this->deposits->count();
    }

    /**
     * Fetch results deposits and insights
     *
     * @return void
     */
    protected function fetch()
    {
        $this->fetchInsights();
        $this->fetchDeposits();
    }

    /**
     * Load deposits from database
     *
     * @return Row
     */
    protected function fetchDeposits()
    {
        $this->deposits = Deposit::allowedOffers()
            ->whereBetween('lead_return_date', [$this->since, $this->until])
            ->where('user_id', $this->user->id)
            ->get();

        return $this;
    }

    /**
     * Load insights from database
     *
     * @return Row
     */
    protected function fetchInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->whereBetween('date', [$this->since, $this->until])
            ->when($this->user, function (Builder $query) {
                return $query->whereIn('account_id', Account::forUsers($this->user->id)->pluck('account_id'));
            })
            ->notEmptyWhere('user_id', $this->user->id)
            ->get();

        return $this;
    }
}
