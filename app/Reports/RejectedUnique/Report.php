<?php

namespace App\Reports\RejectedUnique;

use App\Event;
use App\Lead;
use App\LeadDestination;
use App\LeadOrderAssignment;
use App\Office;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\RejectUnique
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
     * @var array|null
     */
    protected $branches = null;

    /**
     * @var array|null
     */
    protected $officeGroups = null;

    /**
     * @var array|null
     */
    protected $offices = null;

    /**
     * @var array|null
     */
    protected $destinations = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\RejectedUnique\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'branches'     => $request->get('branches'),
            'officeGroups' => $request->get('officeGroups'),
            'offices'      => $request->get('offices'),
        ]);
    }

    /**
     * Report constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forBranches($settings['branches'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null)
            ->forOffices($settings['offices'] ?? null)
            ->forDestinations();
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
        $data = $this->aggregate();

        return [
            'headers' => $this->headers(),
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Collection
     */
    protected function aggregate()
    {
        $failedLeads = $this->failedLeadsOnEvents();

        $assignments = LeadOrderAssignment::visible()
            ->allowedOffers()
            ->withoutDeliveryFailed('%User already exist%')
            ->where(function ($query) {
                return $query->where('lead_order_assignments.delivery_failed', 'ilike', '%User already exist%')
                    ->orWhereIn('lead_order_assignments.status', ['Дубль']);
            })
            ->select([
                DB::raw('leads.phone'),
                DB::raw('count(leads.phone) cnt'),
            ])
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('offices', 'leads_orders.office_id', 'offices.id')
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
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
            })
            ->when($this->offices, fn (Builder $query) => $query->whereIn('offices.id', $this->offices))
            ->groupBy(['leads.phone'])
            ->get()
            ->map(fn ($item) => [
                'phone' => $item->phone,
                'cnt'   => $item->cnt + ($failedLeads->where('phone', $item->phone)->first()->cnt ?? 0)
            ]);

        foreach ($failedLeads as $failed) {
            if ($assignments->where('phone', $failed->phone)->first() === null) {
                $assignments->push(['phone' => $failed->phone, 'cnt' => $failed->cnt]);
            }
        }

        return $assignments;
    }

    /**
     * @return Event[]|array|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    protected function failedLeadsOnEvents()
    {
        return Event::query()
            ->select([
                DB::raw('leads.phone'),
                DB::raw('count(leads.phone) cnt'),
            ])
            ->joinSub(
                Lead::visible()->allowedOffers()->select(['id', 'phone', 'user_id']),
                'leads',
                'events.eventable_id',
                'leads.id'
            )
            ->where('eventable_type', Lead::class)
            ->where('type', Lead::DELIVERY_FAILED)
            ->where(DB::raw('custom_data::text'), 'ilike', '%User already exist%')
            ->when($this->since && $this->until, function (Builder $q) {
                $q->whereBetween(DB::raw('events.created_at::date'), [$this->since, $this->until]);
            })
            ->where(function ($q) {
                foreach ($this->destinations as $destination) {
                    $q->orWhere(DB::raw('custom_data::text'), 'like', '%"destination_id":' . $destination . ',%');
                }

                return $q;
            })
            ->when($this->branches, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->groupBy(['leads.phone'])
            ->get();
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return collect();
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        return [
            'leads'  => $data->count(),
            'unique' => $data->where('cnt', 1)->count(),
        ];
    }

    /**
     * Gets headers of the report
     *
     * @return string[]
     */
    protected function headers()
    {
        return [
            'leads'  => 'leads',
            'unique' => 'unique',
        ];
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
     * @param array|null $branches
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
     * @param array|null $groups
     *
     * @return Report
     */
    public function forOfficeGroups($groups = null)
    {
        $this->officeGroups = $groups;

        return $this;
    }

    /**
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = Office::visible()
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->when($offices, fn ($q) => $q->whereIn('offices.id', Arr::wrap($offices)))
            ->pluck('offices.id')
            ->push(0)
            ->toArray();

        return $this;
    }

    /**
     * @return Report
     */
    public function forDestinations()
    {
        if (!empty($this->offices)) {
            $this->destinations = LeadDestination::query()
                ->when($this->offices, fn (Builder $query) => $query->whereIn('office_id', $this->offices))
                ->pluck('id')
                ->push(0)
                ->unique()
                ->toArray();
        }

        return $this;
    }
}
