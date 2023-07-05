<?php

namespace App\Reports\LeadManagerAssignments;

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
 * @package App\Reports\LeadManagerAssignments
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
     * @var array|null
     */
    protected $managers;

    /**
     * @var array|null
     */
    protected $branches;

    /**
     * @var array|null
     */
    protected $teams;

    /**
     * @var array|null
     */
    protected $users;

    /**
     * @var array|null
     */
    protected $officeGroups;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\LeadManagerAssignments\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'offices'      => $request->get('offices'),
            'offers'       => $request->get('offers'),
            'managers'     => $request->get('managers'),
            'branches'     => $request->get('branches'),
            'teams'        => $request->get('teams'),
            'users'        => $request->get('users'),
            'officeGroups' => $request->get('officeGroups'),
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
            ->forManagers($settings['managers'] ?? null)
            ->forBranches($settings['branches'] ?? null)
            ->forTeams($settings['teams'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\LeadManagerAssignments\Report
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
     * @return \App\Reports\LeadManagerAssignments\Report
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

    public function forManagers($managers = null)
    {
        $this->managers = $managers;

        return $this;
    }

    public function forBranches($branches = null)
    {
        if (!empty($branches)) {
            $this->branches = Arr::wrap($branches);
        }

        return $this;
    }

    public function forTeams($teams = null)
    {
        if (!empty($teams)) {
            $this->teams = Arr::wrap($teams);
        }

        return $this;
    }

    public function forUsers($users = null)
    {
        $this->users = User::visible()
            ->when($users, fn ($q) => $q->whereIn('id', Arr::wrap($users)))
            ->pluck('id')
            ->push(0)
            ->toArray();

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
                'leads_orders.date AS date',
                'offices.name AS office',
                'managers.name AS manager',
                'offers.name AS offer',
                DB::raw('count(lead_order_assignments.id) AS leads'),
                DB::raw('count(CASE WHEN lead_order_assignments.confirmed_at IS NOT NULL THEN 1 END) as confirmed'),
            ])
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('managers', 'lead_order_routes.manager_id', 'managers.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('offices', 'leads_orders.office_id', 'offices.id')
            ->leftJoin('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->when(
                $this->since && $this->until,
                fn ($q) => $q->whereBetween(
                    'lead_order_assignments.created_at',
                    [$this->since->startOfDay(), $this->until->endOfDay()]
                )
            )
            ->when($this->branches, fn (Builder $query) => $query->whereIn('users.branch_id', $this->branches))
            ->when($this->teams, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw(1)
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->users, fn (Builder $query) => $query->whereIn('leads.user_id', $this->users))
            ->notEmptyWhereIn('offices.id', $this->offices)
            ->notEmptyWhereIn('offers.id', $this->offers)
            ->notEmptyWhereIn('managers.id', $this->managers)
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->orderBy('date')
            ->groupBy('date', 'office', 'manager', 'offer')
            ->get();
    }

    /**
     * @return array
     */
    protected function headers()
    {
        return ['date', 'office', 'manager', 'offer', 'leads', 'confirmed'];
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
                'date'           => $row->date,
                'office'         => $row->office,
                'manager'        => $row->manager ?? 'All office',
                'offer'          => $row->offer,
                'leads'          => $row->leads,
                'confirmed'      => $row->confirmed,
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
            'date'      => 'Итого',
            'office'    => '',
            'manager'   => '',
            'offer'     => '',
            'leads'     => $data->sum('leads') ?? 0,
            'confirmed' => $data->sum('confirmed') ?? 0,
        ];
    }
}
