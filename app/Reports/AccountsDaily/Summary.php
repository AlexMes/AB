<?php

namespace App\Reports\AccountsDaily;

use App\Deposit;
use App\Insights;
use App\Metrics\Binom\LandingConversion;
use App\Metrics\PrelandingConversion;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Summary implements Arrayable
{
    /**
     * Since when build summary
     *
     * @var \Carbon\Carbon
     */
    protected $since;

    /**
     * Until when build summary
     *
     * @var \Carbon\Carbon
     */
    protected $until;

    /**
     * Accounts to build report for
     *
     * @var \Illuminate\Support\Collection
     */
    protected $accounts;

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
     * @var Collection|null
     */
    protected $users;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Carbon\Carbon                 $since
     * @param \Carbon\Carbon                 $until
     * @param \Illuminate\Support\Collection $accounts
     * @param Collection|null                $users
     */
    public function __construct(Carbon $since, Carbon $until, Collection $accounts, ?Collection $users = null)
    {
        $this->since    = $since;
        $this->until    = $until;
        $this->accounts = $accounts;
        $this->users    = $users;
    }

    /**
     * Named constructor
     *
     * @param \Carbon\Carbon                 $since
     * @param \Carbon\Carbon                 $until
     * @param \Illuminate\Support\Collection $accounts
     * @param Collection|null                $users
     *
     * @return \App\Reports\AccountsDaily\Summary
     */
    public static function build(Carbon $since, Carbon $until, Collection $accounts, ?Collection $users = null)
    {
        return new static($since, $until, $accounts, $users);
    }

    /**
     * Get array representation of summary
     *
     * @return array
     */
    public function toArray()
    {
        $this->fetchData();

        return [
            Fields::DATE                  => 'ИТОГО',
            Fields::ACCOUNT               => null,
            Fields::IMPRESSIONS           => $this->insights->sum(Fields::IMPRESSIONS),
            Fields::CLICKS                => $this->getClicks() ?? 0,
            Fields::CPM                   => sprintf("\$ %s", round($this->getCpm(), 2)),
            Fields::CPC                   => sprintf("\$ %s", round($this->getCpc(), 2)),
            Fields::CTR                   => sprintf("%s %%", round($this->getCtr(), 2)),
            Fields::CRPL                  => PrelandingConversion::make()
                ->since($this->since)
                ->until($this->until)
                ->metric(),
            Fields::CRLP                  => LandingConversion::make()
                ->since($this->since)
                ->until($this->until)
                ->metric(),
            Fields::LEADS                 => $this->insights->sum(Fields::LEADS),
            Fields::FTD                   => $this->deposits,
            Fields::FTD_PERCENT           => sprintf("%s %%", round($this->getLeadsToFtd(), 2)),
            Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL                   => sprintf("\$ %s", round($this->getCpl(), 2)),
            Fields::COST                  => sprintf("\$ %s", round($this->getCosts(), 2)),
            Fields::PROFIT                => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                   => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    /**
     * Get leads to FTD percentage
     *
     * @return false|float|int
     */
    protected function getLeadsToFtd()
    {
        if ($this->insights->sum(Fields::LEADS) === 0) {
            return 0;
        }

        return ($this->deposits / $this->insights->sum(Fields::LEADS)) * 100;
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
     * Calculate cpl.
     *
     * @return string
     */
    protected function getCpl()
    {
        if ($this->insights->sum(Fields::LEADS) === 0) {
            return 0;
        }

        return $this->getCosts() / $this->insights->sum(Fields::LEADS);
    }

    /**
     * Calculate profit (revenue - costs)
     *
     * @return string
     */
    protected function getProfit()
    {
        return $this->getRevenue() - $this->getCosts();
    }

    /**
     * Calculate ROI (profit/cost)
     *
     * @return string
     */
    protected function getRoi()
    {
        if ($this->getCosts()) {
            return ($this->getProfit() / $this->getCosts()) * 100;
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
        $this->deposits  = Deposit::allowedOffers()
            ->whereBetween('lead_return_date', [$this->since->toDateString(),$this->until->toDateString()])
            ->whereIn('account_id', $this->accounts->pluck('account_id')->values())
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
        $this->insights = Insights::allowedOffers()
            ->whereBetween('date', [$this->since->toDateString(),$this->until->toDateString()])
            ->whereIn('account_id', $this->accounts->pluck('account_id')->values())
            ->when($this->users !== null, fn ($q) => $q->whereIn('user_id', $this->users->pluck('id')))
            ->get();

        return $this;
    }

    /**
     * Get CPM
     * Cost/(Impressions/1000)
     *
     * @return int|float
     */
    protected function getCpm()
    {
        if ($this->insights->sum('impressions')) {
            return $this->getCosts() / ($this->insights->sum('impressions') / 1000);
        }

        return 0;
    }

    /**
     * Get CPC
     * Cost/Clicks
     *
     * @return int|float
     */
    protected function getCpc()
    {
        if ($this->getClicks()) {
            return $this->getCosts() / $this->getClicks();
        }

        return 0;
    }

    /**
     * Get CTR
     * Clicks/Impressions
     *
     *@return int|float
     */
    protected function getCtr()
    {
        if ($this->insights->sum('impressions')) {
            return ($this->getClicks() / $this->insights->sum('impressions')) * 100;
        }

        return 0;
    }

    /**
     * Get costs
     *
     * @return mixed
     */
    protected function getCosts()
    {
        return $this->insights->sum('spend');
    }

    /**
     * Get clicks count
     *
     * @return int
     */
    protected function getClicks()
    {
        return $this->insights->sum('link_clicks') ?? 0;
    }
}
