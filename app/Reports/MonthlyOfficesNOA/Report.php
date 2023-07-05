<?php

namespace App\Reports\MonthlyOfficesNOA;

use App\LeadOrderAssignment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Collection;

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
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now());
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
        $this->since = (is_null($since) ? now() : Carbon::parse($since) ?? now())->startOfDay();

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
        $this->until = (is_null($until) ? now() : Carbon::parse($until) ?? now())->endOfDay();

        return $this;
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $rows = $this->aggregate();

        return [
            'headers' => Fields::ALL,
            'rows'    => $this->rows($rows),
            'summary' => $this->summary($rows)
        ];
    }

    /**
     * Get report rows
     *
     * @param mixed $rows
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function rows($rows)
    {
        return $rows->map(function ($row) {
            return [
                Fields::NAME            => $row->name,
                Fields::LEADS           => $row->leads,
                Fields::NOA             => $row->noa,
                Fields::DUPLICATES      => $row->duplicates,
            ];
        })->values();
    }

    /**
     * Get report summary
     *
     * @param mixed $rows
     *
     * @return array
     */
    public function summary($rows): array
    {
        return [
            Fields::NAME        => 'TOTAL',
            Fields::LEADS       => $rows->sum('leads'),
            Fields::NOA         => $rows->sum('noa'),
            Fields::DUPLICATES  => $rows->sum('duplicates'),
        ];
    }

    /**
     * Load rows
     *
     * @return LeadOrderAssignment[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Collection|void
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::allowedOffers()
            ->select([
                DB::raw('offices.name AS name'),
                DB::raw('count(leads.id) AS leads'),
                DB::raw("count(case when lead_order_assignments.status = 'Нет ответа' then 1 end) as noa"),
                DB::raw("count(case when lead_order_assignments.status = 'Дубль' then 1 end) as duplicates"),
            ])
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'leads_orders.id', 'lead_order_routes.order_id')
            ->join('offices', 'leads_orders.office_id', 'offices.id')
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('offers', 'leads.offer_id', 'offers.id')
            ->leftJoin('deposits', 'leads.id', 'deposits.lead_id')
            ->whereBetween('lead_order_assignments.created_at', [$this->since, $this->until])
            ->where(function ($query) {
                $query->where('offices.frx_tenant_id', 1)->orWhereIn('offices.id', [49, 50, 51, 52]);
            })
            ->groupBy(['offices.name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
