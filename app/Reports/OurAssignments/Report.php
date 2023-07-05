<?php

namespace App\Reports\OurAssignments;

use App\LeadOrderAssignment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
     * @var array|null
     */
    protected $offers;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return self
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'offers'       => $request->get('offers'),
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
            ->forOffers($settings['offers'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return $this
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
     * @return $this
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
     * @param array|null $offers
     *
     * @return $this
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

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
        $data = $this->aggregate();

        return [
            'headers'  => $this->headers(),
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::allowedOffers()
            ->select([
                'leads_orders.date AS date',
                'offers.name AS offer',
                DB::raw('count(lead_order_assignments.id) AS leads'),
                DB::raw('count(deposits.*) as deposits'),
            ])
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->on(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->on('leads_orders.office_id', 'deposits.office_id');
            })
            ->when($this->since && $this->until, fn ($q) => $q->whereBetween('leads_orders.date', [$this->since, $this->until]))
            ->notEmptyWhereIn('offers.id', $this->offers)
            ->whereDoesntHave('lead', fn (Builder $builder) => $builder->whereNotNull('affiliate_id'))
            ->groupBy('leads_orders.date', 'offer')
            ->get();
    }

    /**
     * @return array
     */
    protected function headers()
    {
        return ['date', 'offer', 'leads', 'ftd', 'ftd%'];
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return $data->map(function ($row) {
            return [
                'date'        => $row->date,
                'offer'       => $row->offer,
                'leads'       => $row->leads ?? 0,
                'ftd'         => $row->deposits ?? 0,
                'ftd_percent' => sprintf('%s %%', percentage($row->deposits, $row->leads)),
            ];
        });
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        return [
            'date'        => 'Итого',
            'offer'       => '',
            'leads'       => $data->sum('leads') ?? 0,
            'ftd'         => $data->sum('deposits') ?? 0,
            'ftd_percent' => sprintf('%s %%', percentage($data->sum('deposits'), $data->sum('leads'))),
        ];
    }
}
