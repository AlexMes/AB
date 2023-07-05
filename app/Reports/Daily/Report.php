<?php

namespace App\Reports\Daily;

use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use App\Offer;
use App\Office;
use App\Result;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Class Report
 *
 * @package App\Reports\Daily
 */
class Report implements Responsable, Arrayable
{
    /**
     * Start date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * End date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * Offices used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offices;

    /**
     * Offers used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

    /**
     * Collection of results row
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Trim report and use only data from results
     *
     * @var bool
     */
    protected $showAllDays;

    /**
     * Report variant
     *
     * @var bool
     */
    protected $simple;

    /**
     * Limit results to some users
     *
     * @var array|null
     */
    protected $users;

    /**
     * Limited results for report
     *
     * @var array|null
     */
    protected $results;

    /**
     * Limited deposits for report
     *
     * @var array|null
     */
    protected $deposits;

    /**
     * All deposits by return lead date
     *
     * @var int
     */
    private $returnedDeposits;
    private $insights;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Daily\Report
     */
    public static function fromRequest(\Illuminate\Http\Request $request)
    {
        return new self([
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
            'offices'     => $request->get('offices'),
            'offers'      => $request->get('offers'),
            'showAllDays' => $request->get('showAllDays'),
            'simple'      => $request->get('simple'),
            'users'       => $request->get('users'),
        ]);
    }

    /**
     * DailyReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forOffers($settings['offers'] ?? null)
            ->forOffices($settings['offices'] ?? null)
            ->showAllDays($settings['showAllDays'] ?? false)
            ->simple($settings['simple'] ?? false)
            ->forUsers($settings['users'] ?? null)
            ->getResults()
            ->getDeposits()
            ->getInsights();

        $this->rows = collect();
    }

    /**
     * Get a heading row for the report
     *
     * @return array
     */
    protected function headers()
    {
        return $this->isSimple() ? Headers::SIMPLE : Headers::FULL;
    }

    /**
     * Get report summary
     *
     * @return array
     */
    protected function summary()
    {
        return Summary::build(
            $this->since,
            $this->until,
            $this->offices,
            $this->offers,
            $this->simple,
            $this->users,
            $this->results,
            $this->insights
        )->toArray();
    }

    /**
     * Get report period
     *
     * @return \Carbon\CarbonPeriod
     */
    protected function reportPeriod()
    {
        return (new CarbonPeriod())
            ->days(1)
            ->since($this->since)
            ->until($this->until);
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function collectData()
    {
        if ($this->isSimple()) {
            return $this->allDays();
        }

        return $this->full();
    }

    /**
     * Load all days with stats
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function allDays()
    {
        /** @var Carbon $date */
        foreach ($this->reportPeriod() as $date) {
            /** @var \App\Office $office */
            foreach ($this->offers as $offer) {
                if ($this->isSimple()) {
                    $this->rows->push(
                        (new Row($date, $offer, true, null, $this->users))->toArray()
                    );
                } else {
                    /** @var \App\Offer $offer */
                    foreach ($this->offices as $office) {
                        $this->rows->push(
                            (new Row($date, $offer, false, $office, $this->users))->toArray()
                        );
                    }
                }
            }
        }

        return $this->rows->reject(function ($row) {
            return $row['clicks'] === 0;
        });
    }

    /**
     * Go only for resulting days
     *
     * @return \Illuminate\Support\Collection
     */
    protected function full()
    {
        $this->results->each(function (Result $result) {
            $this->rows->push(
                (new Row($result->date, $result->offer, false, $result->office))->toArray()
            );
        });

        return $this->rows->reject(function ($row) {
            return $row['clicks'] === 0;
        });
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\Daily\Report
     */
    public function since($since = null)
    {
        $this->since = is_null($since) ? now() : Carbon::parse($since) ?? now();

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\Daily\Report
     */
    public function until($until = null)
    {
        $this->until = is_null($until) ? now() : Carbon::parse($until) ?? now();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $late = $this->shouldShowLate() ? $this->late() : null;

        return [
            'headers' => $this->headers(),
            'rows'    => $this->collectData(),
            'summary' => $this->summary(),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
            'returned'  => optional($late)->returnedMetric() ?? [],
            'late'      => optional($late)->lateMetric() ?? [],
        ];
    }

    /**
     * Get JSON representation of report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     * Filter results for specific offers
     *
     * @param array|string $offers
     *
     * @return \App\Reports\Daily\Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = is_null($offers) ? Offer::allowed()->get() : Offer::whereIn('id', Arr::wrap($offers))->get();

        return $this;
    }

    /**
     * Filter results for specific offices
     *
     * @param array|string $offices
     *
     * @return \App\Reports\Daily\Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = is_null($offices) ? Office::all() : Office::whereIn('id', Arr::wrap($offices))->get();

        $this->offices->push(new Office(['id' => null, 'name' => 'Без офиса']));

        return $this;
    }

    /**
     * Determines how to load report
     * all days, or only results
     *
     * @param bool $showAllDays
     *
     * @return \App\Reports\Daily\Report
     */
    protected function showAllDays($showAllDays)
    {
        $this->showAllDays = filter_var($showAllDays, FILTER_VALIDATE_BOOLEAN);

        return $this;
    }

    /**
     * Determines how to load report simple
     * version (buyer default) or full
     *
     * @param bool $simple
     *
     * @return \App\Reports\Daily\Report
     */
    protected function simple($simple = false)
    {
        $this->simple = filter_var($simple, FILTER_VALIDATE_BOOLEAN);

        return $this;
    }

    /**
     * Determines what type of report should be built
     *
     * @return bool
     */
    protected function isSimple()
    {
        return auth()->user()->isBuyer() || $this->simple;
    }

    /**
     * Limit report to certain users
     *
     * @param $users
     *
     * @return \App\Reports\Daily\Report
     */
    protected function forUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get count of leads for report with certain limits
     *
     * @return \App\Reports\Daily\Report
     */
    protected function getResults()
    {
        $this->results = Result::allowedOffers()
            ->whereBetween('date', [$this->since->toDateTimeString(), $this->until->toDateTimeString()])
            ->whereIn('office_id', $this->offices->pluck('id')->values())
            ->whereIn('offer_id', $this->offers->pluck('id')->values())
            ->orderBy('date')
            ->get();

        return $this;
    }

    /**
     * Get deposits for report with certain limits
     *
     * @return \App\Reports\Daily\Report
     */
    protected function getDeposits()
    {
        $this->deposits =  Deposit::visible()
            ->allowedOffers()
            ->whereBetween('lead_return_date', [$this->since->toDateString(), $this->until->toDateString()])
            ->where(function ($query) {
                $query->whereIn('offer_id', $this->offers->pluck('id')->toArray())
                    ->orWhereNull('offer_id');
            })
            ->whereIn('office_id', $this->offices->pluck('id')->toArray())
            ->when($this->users, function (Builder $query) {
                return $query->whereIn('user_id', $this->users);
            })
            ->get();

        return $this;
    }

    /**
     * Load insights from database
     *
     * @return \App\Reports\Daily\Report
     */
    protected function getInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->users, function (Builder $query) {
                return $query->whereIn('account_id', Account::forUsers($this->users)->pluck('account_id'));
            })
            ->notEmptyWhereIn('user_id', $this->users)
            ->whereNotNull('offer_id')
            ->whereIn('offer_id', $this->offers->pluck('id')->values())
            ->get(['link_clicks', 'leads_cnt', 'spend', 'impressions']);

        return $this;
    }


    /**
     * Determines when we show late deposits for user
     *
     * @return bool
     */
    protected function shouldShowLate()
    {
        return $this->until->diffInDays($this->since) < $this->since->daysInMonth;
    }

    /**
     * @return Late
     */
    protected function late()
    {
        return Late::build(
            $this->insights,
            $this->offers,
            $this->deposits,
            $this->results->sum(Fields::LEADS),
            $this->since,
            $this->until,
            $this->isSimple()
        );
    }
}
