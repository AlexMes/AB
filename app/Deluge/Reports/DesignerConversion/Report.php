<?php

namespace App\Deluge\Reports\DesignerConversion;

use App\Deposit;
use App\LeadOrderAssignment;
use App\Offer;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Report implements Arrayable, Responsable
{
    public const DESIGNERS = [
        'denoff',
        'andre',
        'KA',
    ];

    /**
     * @var Carbon
     */
    protected $since;

    /**
     * @var Carbon
     */
    protected $until;


    /**
     * @var array|null
     */
    protected ?array $branchUsers;

    /**
     * @var array|null
     */
    protected ?array $teams;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var string
     */
    protected string $sortBy = 'name';

    /**
     * @var boolean
     */
    protected bool $descending = false;

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
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'branches'      => $request->get('branches'),
            'teams'         => $request->get('teams'),
            'vertical'      => $request->get('vertical'),
            'sort_by'       => $request->get('sort'),
            'descending'    => $request->boolean('desc'),
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
        $this->since($settings['since'] ?? now()->startOfMonth())
            ->until($settings['until'] ?? now())
            ->forBranches($settings['branches'])
            ->forTeams($settings['teams'])
            ->forVertical($settings['vertical']);

        $this->sortBy($settings['sort_by'], $settings['descending']);
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
     * @inheritDoc
     */
    public function toArray()
    {
        $data = $this->getReportData();

        return [
            'headers' => $this->headers(),
            'rows'    => $this->rows($data)->toArray(),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    protected function headers()
    {
        return [
            'name'  => 'designer',
            'leads' => 'leads',
            'ftd'   => 'ftd',
            'rate'  => 'rate',
        ];
    }

    /**
     * Get rows
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(function ($item) {
            return [
                'name'  => $item->designer ?: 'Остальные',
                'leads' => $item->leads ?? 0,
                'ftd'   => $item->ftd ?? 0,
                'rate'  => sprintf('%s %%', percentage($item->ftd, $item->leads)),
            ];
        })->sortBy($this->sortBy, SORT_REGULAR, $this->descending);
    }

    /**
     * Get summary
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary($data)
    {
        return [
            'name'  => 'Итого',
            'leads' => $data->sum('leads') ?? 0,
            'ftd'   => $data->sum('ftd') ?? 0,
            'rate'  => sprintf('%s %%', percentage($data->sum('ftd'), $data->sum('leads'))),
        ];
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return LeadOrderAssignment::allowedOffers()
            ->select([
                DB::raw($this->getDesignerSelect()),
                DB::raw('count(lead_order_assignments.id) AS leads'),
                DB::raw('count(deps.id) AS ftd'),
            ])
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoinSub(
                Deposit::allowedOffers()
                    ->select(['id', 'lead_id', 'lead_return_date', 'office_id'])
                    ->whereBetween('date', [$this->since,$this->until])
                    ->when($this->branchUsers, fn ($query) => $query->whereIn('deposits.user_id', $this->branchUsers))
                    ->when($this->verticalOffers, fn ($query) => $query->whereIn('deposits.offer_id', $this->verticalOffers))
                    ->when($this->teams, function (Builder $builder) {
                        return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                            return $query->select(DB::raw(1))
                                ->from('team_user')
                                ->whereColumn('team_user.user_id', 'deposits.user_id')
                                ->whereIn('team_user.team_id', $this->teams);
                        });
                    }),
                'deps',
                function (JoinClause $join) {
                    return $join->on('lead_order_assignments.lead_id', 'deps.lead_id')
                        ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deps.lead_return_date')
                        ->whereColumn('leads_orders.office_id', 'deps.office_id');
                }
            )
            ->whereBetween(DB::raw('lead_order_assignments.registered_at'), [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->branchUsers, fn ($query) => $query->whereIn('leads.user_id', $this->branchUsers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('lead_order_routes.offer_id', $this->verticalOffers))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->whereNull('affiliate_id')
            ->groupBy('designer')
            ->get();
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
            $this->since = now()->startOfMonth();

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
     * Filters by branches
     *
     * @param array|null $branches
     *
     * @return $this
     */
    public function forBranches(?array $branches = null)
    {
        if ($branches !== null) {
            $this->branchUsers = User::query()
                ->whereIn('branch_id', $branches)
                ->pluck('id')
                ->push(0)
                ->toArray();
        } else {
            $this->branchUsers = null;
        }

        return $this;
    }

    /**
     * Filter by teams
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
     * Filters by offer's vertical
     *
     * @param string|null $vertical
     *
     * @return $this
     */
    public function forVertical(?string $vertical = null)
    {
        if ($vertical !== null) {
            $this->verticalOffers = Offer::allowed()
                ->where('vertical', $vertical)
                ->pluck('id')
                ->push(0)
                ->toArray();
        } else {
            $this->verticalOffers = null;
        }

        return $this;
    }

    /**
     * @param string|null $sortBy
     * @param bool|null   $descending
     *
     * @return $this
     */
    public function sortBy(?string $sortBy, ?bool $descending = false)
    {
        if ($sortBy !== null && array_key_exists($sortBy, $this->headers())) {
            $this->sortBy = $sortBy;
        }

        $this->descending = $descending ?? false;

        return $this;
    }

    /**
     * @return string
     */
    protected function getDesignerSelect()
    {
        $result = 'CASE';

        foreach (static::DESIGNERS as $designer) {
            $result .= " WHEN leads.utm_content LIKE '%{$designer}%' THEN '{$designer}'";
        }

        return $result . ' END AS designer';
    }
}
