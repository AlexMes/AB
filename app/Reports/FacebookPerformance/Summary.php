<?php

namespace App\Reports\FacebookPerformance;

use App\Insights;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\Impressions;
use App\Metrics\Facebook\LeadsCount;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
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
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

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
     * @var bool
     */
    protected bool $hideFacebook;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     * @param bool                           $hideFacebook
     */
    public function __construct(Collection $insights, Collection $deposits, $level, $traffic = null, $hideFacebook = false)
    {
        $this->insights       = $insights;
        $this->deposits       = $deposits->count();
        $this->level          = $level;
        $this->traffic        = $traffic;
        $this->hideFacebook   = $hideFacebook;
    }

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param \Illuminate\Support\Collection $deposits
     * @param mixed                          $level
     * @param null                           $traffic
     * @param mixed                          $hideFb
     *
     * @return \App\Reports\FacebookPerformance\Summary
     */
    public static function build(Collection $insights, Collection $deposits, $level, $traffic = null, $hideFb = false)
    {
        return new self($insights, $deposits, $level, $traffic, $hideFb);
    }

    /**
     * Get array representation of summary
     *
     * @return array
     */
    public function toArray()
    {
        $row = array_merge(
            $this->defaults(),
            $this->conversions(),
            $this->aggregates()
        );

        return $this->hideFacebook ? Arr::except($row, Fields::fbKeys()) : $row;
    }

    /**
     * Get default values for report
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            Fields::NAME                  => 'ИТОГО',
            Fields::RK_COUNT              => $this->insights->unique('account_id')->count(),
            Fields::IMPRESSIONS           => Impressions::make($this->insights)->metric(),
            Fields::CLICKS                => Clicks::make($this->insights)->metric(),
            Fields::CPM                   => CPM::make($this->insights)->metric(),
            Fields::CPC                   => CPC::make($this->insights)->metric(),
            Fields::CTR                   => CTR::make($this->insights)->metric(),
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
        return [
            Fields::LPVIEWS               => $this->traffic->sum('lp_views') ,
            Fields::LPCLICKS              => $this->traffic->sum('lp_clicks'),
            Fields::LEADS                 => LeadsCount::make($this->insights)->metric(),
            Fields::BLEADS                => $this->traffic->sum('leads'),
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
        return [
            Fields::CPL                   => CPL::make($this->insights)->metric(),
            Fields::COST                  => Cost::make($this->insights)->metric(),
        ];
    }
}
