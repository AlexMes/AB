<?php

namespace App\Reports\OperatingSystem;

use App\LeadOrderAssignment;
use App\PlatformInsights;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
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
     * Devices filter
     *
     * @var array
     */
    protected $devices = true;

    /**
     * Operating systems filter
     *
     * @var array
     */
    protected $oss;

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
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
            'devices'  => $request->get('devices'),
            'oss'      => $request->get('oss'),
            'teams'    => $request->get('teams'),
        ]);
    }

    /**
     * GenderReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forDevices($settings['devices'])
            ->forOperatingSystems($settings['oss'])
            ->forTeams($settings['teams']);
    }

    /**
     * Filter by device type
     *
     * @param array|null $devices
     *
     * @return void
     */
    public function forDevices($devices = null)
    {
        $this->devices = $devices;

        return $this;
    }

    /**
     * Filter by operating system
     *
     * @param array|null $oss
     *
     * @return void
     */
    public function forOperatingSystems($oss = null)
    {
        $this->oss = $oss;

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
     * @inheritDoc
     */
    public function toArray()
    {
        $rows = $this->aggregate();

        return [
            'headers' => ['device','os', 'leads', 'ftd','ftd %', 'cpl'],
            'rows'    => $this->rows($rows),
            'summary' => $this->summary($rows),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    /**
     * Get rows
     *
     * @param $rows
     *
     * @return \App\Deposit[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function rows($rows)
    {
        return $rows->map(fn ($row) => [
            'device'     => $row->device ?? 'Unknown',
            'os'         => $row->os ?? 'Unknown',
            'leads'      => $row->leads,
            'ftd'        => $row->ftd,
            'conversion' => $this->percentage($row->ftd, $row->leads),
            'cpl'        => $row->leads != 0 ? round($row->spend / $row->leads, 2) : 0,
        ]);
    }

    /**
     * Get summary
     *
     * @param $rows
     *
     * @return array
     */
    protected function summary($rows)
    {
        return [
            'device'     => 'TOTAL',
            'os'         => '',
            'leads'      => $rows->sum('leads'),
            'ftd'        => $rows->sum('ftd'),
            'conversion' => $this->percentage($rows->sum('ftd'), $rows->sum('leads')),
            'cpl'        => $rows->sum('spend') != 0 ? round($rows->sum('spend') / $rows->sum('leads'), 2) : 0,
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
     * @return \Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::visible()->select([
            DB::raw(
                'binom_clicks.device_pointing_method AS device'
            ),
            DB::raw('binom_clicks.os_name AS os'),
            DB::raw('count(lead_order_assignments.id) as leads'),
            DB::raw('count(deposits.id) as ftd'),
            DB::raw('COALESCE(platform.spend, 0) as spend'),
        ])
            ->allowedOffers()
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('binom_clicks', 'lead_order_assignments.lead_id', '=', 'binom_clicks.lead_id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->leftJoinSub($this->insights(), 'platform', function (JoinClause $join) {
                $join->on('binom_clicks.device_pointing_method', '=', 'platform.device_type')
                    ->on('binom_clicks.os_name', '=', 'platform.device_os');
            })
            ->whereBetween('lead_order_assignments.registered_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->when($this->devices, fn ($query) => $query->whereIn('binom_clicks.device_pointing_method', $this->devices))
            ->when($this->oss, fn ($query) => $query->whereIn('binom_clicks.os_name', $this->oss))
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->selectRaw('1')
                        ->from('team_user')
                        ->whereColumn('leads.user_id', 'team_user.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->groupBy(['device', 'os', 'spend'])
            ->get();
    }

    protected function insights()
    {
        return PlatformInsights::select([
            DB::raw('sum(spend::decimal) as spend'),
            DB::raw("
                case
                    when impression_device in ('android_tablet', 'android_smartphone') then 'Android'
                    when impression_device in ('ipad', 'ipod', 'iphone') then 'iOS'
                end as device_os
            "),
            DB::raw("
                case
                    when impression_device in ('android_smartphone', 'iphone', 'ipod') then 'Smartphone'
                    when impression_device in ('android_tablet', 'ipad') then 'Tablet'
                end as device_type
            "),
        ])
            ->whereBetween('date', [$this->since, $this->until])
            ->unless(auth()->user()->isAdmin(), function (Builder $query) {
                return $query->whereIn(
                    'facebook_platform_insights.offer_id',
                    auth()->user()->allowedOffers->pluck('id')->values()
                );
            })
            ->groupBy('impression_device');
    }
}
