<?php

namespace App\Reports\OfficePerformanceCopy;

use App\Deposit;
use App\LeadOrderAssignment;
use App\Offer;
use App\Office;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
     * Branches used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $branches;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $officeGroups;

    /**
     * @var Deposit[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $results;

    /**
     * @var boolean
     */
    protected $groupByUtmSource;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'            => $request->get('since'),
            'until'            => $request->get('until'),
            'offices'          => $request->get('offices'),
            'offers'           => $request->get('offers'),
            'branches'         => $request->get('branches'),
            'officeGroups'     => $request->get('officeGroups'),
            'groupByUtmSource' => $request->boolean('groupByUtmSource', false),
        ]);
    }

    /**
     * Constructor.
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
            ->forBranches($settings['branches'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null)
            ->groupByUtmSource($settings['groupByUtmSource'] ?? false);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return Report
     */
    public function since($since = null)
    {
        if (is_null($since)) {
            $this->since = now();

            return $this;
        }

        $this->since = Carbon::parse($since);

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return Report
     */
    public function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * Filter results for specific offers
     *
     * @param array|string $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = is_null($offers) ? Offer::allowed()->pluck('id')->values() : Offer::whereIn('id', Arr::wrap($offers))->pluck('id')->values();

        return $this;
    }

    /**
     * Filter results for specific offices
     *
     * @param array|string $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = is_null($offices) ? Office::visible()->pluck('id')->values() : Office::whereIn('id', Arr::wrap($offices))->pluck('id')->values();

        $this->offices->add(null);

        return $this;
    }

    /**
     * Filter results for specific branches
     *
     * @param array|string $branches
     *
     * @return Report
     */
    public function forBranches($branches = null)
    {
        if (!empty($branches)) {
            $this->branches = Arr::wrap($branches);
        }

        return $this;
    }

    /**
     * @param array|string $groups
     *
     * @return Report
     */
    public function forOfficeGroups($groups = null)
    {
        if (!empty($groups)) {
            $this->officeGroups = $groups;
        }

        return $this;
    }

    /**
     * @param bool $groupByUtmSource
     *
     * @return \App\Reports\OfficePerformanceCopy\Report
     */
    public function groupByUtmSource(bool $groupByUtmSource = true)
    {
        $this->groupByUtmSource = $groupByUtmSource;

        return $this;
    }

    /**
     * Get response representation of the report
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
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        $this->aggregate();

        return [
            'headers'  => $this->headers(),
            'rows'     => $this->rows(),
            'summary'  => $this->summary(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return void
     */
    protected function aggregate()
    {
        $this->results = DB::query()
            ->selectRaw("
                offices.name as office,
                offers.name as offer,
                COALESCE(ROUND(sum(lead_order.leads) / count(lead_order.*)), 0) AS leads,
                COALESCE(ROUND(sum(deps.deposits) / count(deps.*)), 0) AS deposits,
                COALESCE(ROUND(sum(deps.intradep) / count(deps.*)), 0) AS intradep,
                COALESCE(ROUND(sum(deps.late) / count(deps.*)), 0) AS late
            ")
            ->from('offices')
            ->crossJoin('offers')
            ->when($this->groupByUtmSource, function ($query) {
                return $query->leftJoin('leads', 'leads.offer_id', '=', 'offers.id');
            })
            ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                return $join->on('offices.id', 'deps.office_id')
                    ->on('offers.id', 'deps.offer_id')
                    ->when($this->groupByUtmSource, function (JoinClause $joinSource) {
                        return $joinSource->on('leads.utm_source', 'deps.utm_source');
                    });
            })
            ->leftJoinSub($this->leads(), 'lead_order', function (JoinClause $join) {
                return $join->on('offices.id', 'lead_order.office_id')
                    ->on('offers.id', 'lead_order.offer_id')
                    ->when($this->groupByUtmSource, function (JoinClause $joinSource) {
                        return $joinSource->on('leads.utm_source', 'lead_order.utm_source');
                    });
            })
            ->when($this->groupByUtmSource, function ($query) {
                return $query
                    ->selectRaw(
                        "CASE
                        WHEN leads.utm_source is null THEN ''
                        WHEN leads.utm_source is not null THEN leads.utm_source
                    END as utm_sources"
                    )->groupBy('utm_sources');
            })
            ->groupBy(['office', 'offer'])
            ->havingRaw('sum(lead_order.leads) > 0')
            ->orHavingRaw('sum(deps.deposits) > 0')
            ->get();
    }

    protected function leads()
    {
        return LeadOrderAssignment::visible()
            ->selectRaw("
                leads_orders.office_id,
                lead_order_routes.offer_id,
                count(lead_order_assignments.id) AS leads
            ")
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->branches, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })->when($this->groupByUtmSource, function ($query) {
                return $query
                    ->selectRaw(
                        "CASE
                        WHEN leads.utm_source is null THEN ''
                        WHEN leads.utm_source is not null THEN leads.utm_source
                    END as utm_source"
                    )->groupBy('utm_source');
            })
            ->when($this->offices, fn (Builder $builder) => $builder->whereIn('leads_orders.office_id', $this->offices))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('lead_order_routes.offer_id', $this->offers))
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id']);
    }

    /**
     * @return Deposit|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function deposits()
    {
        return Deposit::visible()
            ->select([
                DB::raw('deposits.office_id'),
                DB::raw('deposits.offer_id'),
                DB::raw('count(deposits.id) as deposits'),
                DB::raw("count(case when deposits.lead_return_date between '".$this->since->toDateString()."' and '".$this->until->toDateString()."' then 1 end ) as intradep"),
                DB::raw("count(case when deposits.lead_return_date < '".$this->since->toDateString()."' then 1 end ) as late"),
            ])
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween('deposits.date', [$this->since, $this->until]);
            })
            ->when($this->branches, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'deposits.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->when($this->groupByUtmSource, function ($query) {
                return $query->selectRaw(
                    "CASE
                        WHEN leads.utm_source is null THEN ''
                        WHEN leads.utm_source is not null THEN leads.utm_source
                    END as utm_source"
                )->leftJoin('leads', 'deposits.lead_id', 'leads.id')
                    ->groupBy('leads.utm_source');
            })
            ->when($this->offices, fn (Builder $query) => $query->whereIn('deposits.office_id', $this->offices))
            ->when($this->offers, fn (Builder $query) => $query->whereIn('deposits.offer_id', $this->offers))
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->groupBy(['deposits.office_id', 'deposits.offer_id']);
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function rows()
    {
        return $this->results->map(function ($row) {
            $result = [
                Fields::OFFICE           => $row->office,
                Fields::OFFER            => $row->offer,
                Fields::UTM_SOURCE       => $row->utm_sources ?? null,
                Fields::LEADS            => $row->leads ?: 0,
                Fields::DEPOSITS         => $row->intradep ?: 0,
                Fields::CONVERSION       => percentage($row->intradep, $row->leads),
                Fields::LATE_DEPOSITS    => $row->late ?: 0,
                Fields::LATE_CONVERSION  => percentage($row->late, $row->leads),
                Fields::TOTAL_DEPOSITS   => $row->deposits,
                Fields::TOTAL_CONVERSION => percentage($row->deposits, $row->leads),
            ];

            if (!$this->groupByUtmSource) {
                unset($result[Fields::UTM_SOURCE]);
            }

            return $result;
        });
    }

    /**
     * @return array
     */
    protected function summary()
    {
        $result = [
            Fields::OFFICE           => 'Итого',
            Fields::OFFER            => '',
            Fields::UTM_SOURCE       => '',
            Fields::LEADS            => $this->results->sum('leads') ?: 0,
            Fields::DEPOSITS         => $this->results->sum('intradep') ?: 0,
            Fields::CONVERSION       => percentage($this->results->sum('intradep'), $this->results->sum('leads')),
            Fields::LATE_DEPOSITS    => $this->results->sum('late'),
            Fields::LATE_CONVERSION  => percentage($this->results->sum('late'), $this->results->sum('leads')),
            Fields::TOTAL_DEPOSITS   => $this->results->sum('deposits'),
            Fields::TOTAL_CONVERSION => percentage($this->results->sum('deposits'), $this->results->sum('leads')),
        ];

        if (!$this->groupByUtmSource) {
            unset($result[Fields::UTM_SOURCE]);
        }

        return $result;
    }

    protected function headers()
    {
        $result = Headers::ALL;

        $except = [];

        if (!$this->groupByUtmSource) {
            $except[] = Headers::UTM_SOURCE;
        }

        $result = array_filter($result, fn ($header) => !in_array($header, $except));

        return $result;
    }
}
