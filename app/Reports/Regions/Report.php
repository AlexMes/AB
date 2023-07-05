<?php

namespace App\Reports\Regions;

use App\LeadOrderAssignment;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Report implements Arrayable, Responsable
{
    /**
     * @var Carbon
     */
    protected $since;
    /**
     * @var Carbon
     */
    protected $until;

    /**
     * Teams filter
     *
     * @var array
     */
    protected $teams;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Regions\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
            'teams'    => $request->get('teams'),
        ]);
    }

    /**
     * RegionsReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forTeams($settings['teams']);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $regions = $this->getReportData();

        return [
            'headers' => ['region','leads','ftd','ftd %'],
            'rows'    => $this->rows($regions)->toArray(),
            'summary' => $this->summary($regions),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    /**
     * Get rows
     *
     * @param \Illuminate\Support\Collection $regions
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($regions)
    {
        return $regions->map(function ($region) {
            $region->ftdPercent = $this->percentage($region->deposits_count, $region->leads_count);
            $region->region = $region->region ?? 'Unknown';

            return $region;
        });
    }

    /**
     * Get summary
     *
     * @param \Illuminate\Support\Collection $regions
     *
     * @return array
     */
    protected function summary($regions)
    {
        return [
            'region'     => 'ИТОГО',
            'cnt'        => $regions->sum('leads_count'),
            'ftd'        => $regions->sum('deposits_count'),
            'ftdPercent' => $this->percentage($regions->sum('deposits_count'), $regions->sum('leads_count')),
        ];
    }

    protected function percentage($one, $two)
    {
        if ($two) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
    }

    /**
     * Report data, formatted by database
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return LeadOrderAssignment::visible()->allowedOffers()
            ->select([
                'ip_addresses.region',
                DB::raw('count(lead_order_assignments.id) leads_count'),
                DB::raw('count(deposits.id) AS deposits_count'),
            ])
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('ip_addresses', 'leads.ip', '=', 'ip_addresses.ip')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->whereBetween('lead_order_assignments.registered_at', [
                $this->since->startOfDay()->toDateTimeString(),
                $this->until->endOfDay()->toDateTimeString(),
            ])
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->selectRaw('1')
                        ->from('team_user')
                        ->whereColumn('leads.user_id', 'team_user.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->groupBy(['ip_addresses.region'])
            ->orderBy('ip_addresses.region')
            ->get();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\Regions\Report
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
     * @return \App\Reports\Regions\Report
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
     * Filter by team
     *
     * @param null|array $teams
     *
     * @return $this
     */
    public function forTeams($teams = null)
    {
        $this->teams = $teams;

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
}
