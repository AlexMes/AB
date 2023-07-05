<?php

namespace App\Reports\LeadOfficeAssignments;

use App\LeadOrderAssignment;
use App\User;
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
 * @package App\Reports\LeadOfficeAssignments
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
    protected $offices;

    /**
     * @var array|null
     */
    protected $offers;

    /**
     * @var string|null
     */
    protected $traffic;

    protected $branch;
    protected $team;
    protected $user;

    /**
     * @var array|null
     */
    protected $officeGroups;


    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\LeadOfficeAssignments\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'offices'       => $request->get('offices'),
            'offers'        => $request->get('offers'),
            'traffic'       => $request->get('traffic'),
            'branch'        => $request->get('branch_id'),
            'team'          => $request->get('team_id'),
            'user'          => $request->get('user_id'),
            'office_groups' => $request->get('office_groups'),
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
            ->forOffices($settings['offices'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->forTraffic($settings['traffic'] ?? null)
            ->forBranch($settings['branch'] ?? null)
            ->forTeam($settings['team'] ?? null)
            ->forUser($settings['user'] ?? null)
            ->forOfficeGroups($settings['office_groups'] ?? null);
    }


    /**
     * Scope report results for branch
     *
     * @param [type] $branch
     *
     * @return static
     */
    public function forBranch($branch = null)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Scope report results for team
     *
     * @param [type]     $branch
     * @param null|mixed $team
     *
     * @return static
     */
    public function forTeam($team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Scope report results for user
     *
     * @param [type]     $branch
     * @param null|mixed $user
     *
     * @return static
     */
    public function forUser($user = null)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\LeadOfficeAssignments\Report
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
     * @return \App\Reports\LeadOfficeAssignments\Report
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
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = $offices;

        return $this;
    }

    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    public function forTraffic($traffic = null)
    {
        $this->traffic = $traffic;

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
            ->visible()
            ->select([
                'offices.name AS office',
                DB::raw('count(lead_order_assignments.id) AS leads'),
            ])
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('offices', 'leads_orders.office_id', 'offices.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->leftJoin('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->when(
                $this->since && $this->until,
                fn ($q) => $q->whereBetween(
                    'lead_order_assignments.created_at',
                    [$this->since->startOfDay(), $this->until->endOfDay()]
                )
            )
            ->notEmptyWhereIn('offices.id', $this->offices)
            ->notEmptyWhereIn('offers.id', $this->offers)
            ->when($this->branch, fn (Builder $query) => $query->where('users.branch_id', $this->branch))
            ->when($this->team, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw(1)
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'leads.user_id')
                        ->where('team_user.team_id', $this->team);
                });
            })
            ->when($this->user, function (Builder $builder) {
                if ($this->user === 'null') {
                    return $builder->whereNull('leads.user_id');
                }

                return $builder->where('leads.user_id', $this->user);
            })
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->when($this->traffic === 'own', fn ($q) => $q->whereNull('leads.affiliate_id'))
            ->when($this->traffic === 'affiliate', fn ($q) => $q->whereNotNull('leads.affiliate_id'))
            ->orderBy('office')
            ->groupBy('office')
            ->get();
    }

    /**
     * @return array
     */
    protected function headers()
    {
        return ['office', 'leads'];
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
                'office'         => $row->office,
                'leads'          => $row->leads,
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
            'office' => 'Итого',
            'leads'  => $data->sum('leads') ?? 0,
        ];
    }
}
