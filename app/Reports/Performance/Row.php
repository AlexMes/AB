<?php

namespace App\Reports\Performance;

use App\Deposit;
use App\Insights;
use App\Lead;
use App\Metrics\Binom\LandingConversion;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\Impressions;
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
     * Number of deposits
     *
     * @var int
     */
    protected $deposits = 0;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Ads account
     *
     * @var \App\Facebook\Account|\App\Facebook\AdSet|\App\Facebook\Campaign|\App\Facebook\Ad
     */
    protected $object;

    /**
     * Level of report
     *
     * @var string
     */
    protected $level;

    /**
     * DailyReportRow constructor.
     *
     * @param \Carbon\Carbon $date
     * @param $object
     * @param $level
     */
    public function __construct(Carbon $date, $object, $level)
    {
        $this->date    = $date;
        $this->object  = $object;
        $this->level   = $level;
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
            Fields::DATE                  => $this->date->toDateString(),
            Fields::NAME                  => $this->object->name,
            Fields::IMPRESSIONS           => Impressions::make($this->insights)->metric(),
            Fields::CLICKS                => Clicks::make($this->insights)->metric(),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
            Fields::CRPL                  => PrelandingConversion::make()->forDate($this->date)->metric(),
            Fields::CRLP                  => LandingConversion::make()->forDate($this->date)->metric(),
            Fields::CR                    => ($this->deposits / LeadsCount::make($this->insights)->value() * 100),
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::FTD                   => $this->deposits,
            Fields::FTD_PERCENT           => sprintf("%s %%", round($this->getLeadsToFtd(), 2)),
            Fields::REVENUE               => sprintf("\$ %s", round($this->getRevenue(), 2)),
            Fields::CPL                   => sprintf("\$ %s", round($this->getCpl(), 2)),
            Fields::COST                  => Cost::make($this->insights)->metric(),
            Fields::PROFIT                => sprintf("\$ %s", round($this->getProfit(), 2)),
            Fields::ROI                   => sprintf("%s %%", round($this->getRoi(), 2)),
        ];
    }

    
    public function conversionRate()
    {
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
        if ($this->level === Report::LEVEL_ACCOUNT) {
            // OK, from the start
            $this->deposits  = Deposit::query()
                ->whereDate('lead_return_date', $this->date->toDateString())
                ->where('account_id', $this->object->account_id)
                ->count();
        }

        if ($this->level === Report::LEVEL_CAMPAIGN) {
            // OK, should check
            $this->deposits = Deposit::query()
                ->whereDate('lead_return_date', $this->date->toDateString())
                ->whereIn(
                    'lead_id',
                    Lead::where(
                        'campaign_id',
                        $this->object->id
                    )->pluck('id')->values()
                )->count();
        }


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
            ->whereDate('date', $this->date->toDateString())
            ->when($this->level === Report::LEVEL_ACCOUNT, function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                return $query->where('account_id', $this->object->account_id);
            })
            ->unless($this->level === Report::LEVEL_ACCOUNT, function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                return $query->where(sprintf("%s_id", $this->level), $this->object->id);
            })
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
            return ($this->getClicks() / $this->insights->sum('impressions') * 100);
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
     * Get links clicks count
     *
     * @return int
     */
    protected function getClicks()
    {
        return $this->insights->sum('link_clicks') ?? 0;
    }
}
