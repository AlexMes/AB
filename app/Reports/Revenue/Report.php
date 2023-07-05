<?php

namespace App\Reports\Revenue;

use App\Deposit;
use App\LeadOrderAssignment;
use App\Office;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Report implements Arrayable, Responsable
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
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Revenue\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
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
            ->until($settings['until'] ?? now());
    }


    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\Revenue\Report
     */
    public function since($since = null)
    {
        if (is_null($since)) {
            $this->since = now()->startOfDay();

            return $this;
        }

        $this->since = Carbon::parse($since)->startOfDay();

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\Revenue\Report
     */
    public function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now()->endOfDay();

            return $this;
        }

        $this->until = Carbon::parse($until)->endOfDay();

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
            'headers'  => ['office','cpl','leads','rev.cpl','cpa','ftd','ftd %','rev.cpa','rev.total'],
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    protected function aggregate()
    {
        return Office::select([
            'name',
            'cpl',
            'office_leads.leads',
            'cpa',
            'office_deposits.deposits',
            DB::raw('COALESCE(offices.cpl * office_leads.leads,0) as revCpl'),
            DB::raw('ROUND(office_deposits.deposits::decimal / nullif(office_leads.leads::decimal, 0) * 100, 2) as cr'),
            DB::raw('COALESCE(offices.cpa * office_deposits.deposits,0) as revCpa'),
            DB::raw('offices.cpa * office_deposits.deposits + offices.cpl * office_leads.leads as revTotal'),
        ])
            ->leftJoinSub($this->leads(), 'office_leads', 'offices.id', '=', 'office_leads.office_id')
            ->leftJoinSub($this->deposits(), 'office_deposits', 'offices.id', '=', 'office_deposits.office_id')
            ->get();
    }

    protected function rows(Collection $data)
    {
        return $data->map(fn ($office) => [
            'office'   => $office->name,
            'cpl'      => $office->cpl,
            'leads'    => $office->leads ?? 0,
            'revCpl'   => $office->revcpl,
            'cpa'      => $office->cpa,
            'ftd'      => $office->deposits ?? 0,
            'ftd_p'    => $office->cr ?? 0,
            'revCpa'   => $office->revcpa,
            'revTotal' => $office->revtotal ?? 0,
        ])->reject(fn ($office) => $office['revTotal'] == 0)->toArray();
    }

    /**
     * Build totals
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        return [
            'office'   => 'Total',
            'cpl'      => '',
            'leads'    => $data->sum('leads'),
            'revCpl'   => $data->sum('revcpl'),
            'cpa'      => '',
            'ftd'      => $data->sum('deposits'),
            'ftd_p'    => percentage($data->sum('leads'), $data->sum('deposits')),
            'revCpa'   => $data->sum('revcpa'),
            'revTotal' => $data->sum('revcpl') + $data->sum('revcpa'),
        ];
    }

    /**
     * Leads query for report
     *
     * @return \Illuminate\Support\Collection
     */
    protected function leads()
    {
        return LeadOrderAssignment::allowedOffers()
            ->select([
                DB::raw('leads_orders.office_id as office_id'),
                DB::raw('count(lead_order_assignments.id) as leads')
            ])
            ->confirmed()
            ->whereBetween('lead_order_assignments.created_at', [$this->since,$this->until])
            ->join('lead_order_routes', 'lead_order_assignments.route_id', '=', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->groupBy('office_id');
    }

    /**
     * Deposits query for report
     *
     * @return \App\Deposit|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function deposits()
    {
        return Deposit::allowedOffers()
            ->select([
                'office_id',
                DB::raw('count(deposits.id) as deposits')
            ])
            ->whereBetween('deposits.created_at', [$this->since, $this->until])
            ->groupBy('office_id');
    }
}
