<?php

namespace App\Reports\AssignmentLabels;

use App\AssignmentDayToDaySnapshot;
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
     * @return \App\Reports\MonthlyDtdNOA\Report
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
                Fields::UTM_SOURCE      => $row->utm_source,
                Fields::UTM_CAMPAIGN    => $row->utm_campaign,
                Fields::UTM_CONTENT     => $row->utm_content,
                Fields::LEADS           => $row->leads,
                Fields::UNDERAGE        => $row->underage,
                Fields::WRONGRESIDENCE  => $row->wrongResidence,
                Fields::SOUTHGUEST      => $row->southGuest,
                Fields::NOREGISTRATION  => $row->noRegistration,

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
            Fields::UTM_SOURCE      => 'TOTAL',
            Fields::UTM_CAMPAIGN    => '',
            Fields::UTM_CONTENT     => '',
            Fields::LEADS           => $rows->sum('leads'),
            Fields::UNDERAGE        => $rows->sum('underage'),
            Fields::WRONGRESIDENCE  => $rows->sum('wrongResidence'),
            Fields::SOUTHGUEST      => $rows->sum('southGuest'),
            Fields::NOREGISTRATION  => $rows->sum('noRegistration'),
        ];
    }

    /**
     * Load rows
     *
     * @return AssignmentDayToDaySnapshot[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Collection|void
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::visible()->allowedOffers()
            ->select([
                DB::raw('leads.utm_source AS utm_source'),
                DB::raw('leads.utm_campaign AS utm_campaign'),
                DB::raw('leads.utm_content AS utm_content'),
                DB::raw('count(leads.id) AS leads'),
                DB::raw('count(CASE WHEN label_lead_order_assignment.label_id = 3 THEN 1 END) AS underage'),
                DB::raw('count(CASE WHEN label_lead_order_assignment.label_id = 4 THEN 1 END) AS wrongResidence'),
                DB::raw('count(CASE WHEN label_lead_order_assignment.label_id = 7 THEN 1 END) AS southGuest'),
                DB::raw('count(CASE WHEN label_lead_order_assignment.label_id = 8 THEN 1 END) AS noRegistration')
            ])
            ->leftJoin('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->leftJoin('label_lead_order_assignment', 'lead_order_assignments.id', 'label_lead_order_assignment.assignment_id')
            ->whereBetween('lead_order_assignments.created_at', [$this->since, $this->until])
            ->groupBy(['leads.utm_source', 'leads.utm_campaign', 'leads.utm_content'])
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
