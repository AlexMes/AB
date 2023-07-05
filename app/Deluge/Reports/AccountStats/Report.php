<?php

namespace App\Deluge\Reports\AccountStats;

use App\ManualInsight;
use App\ManualPour;
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
    protected ?array $users;

    /**
     * @var array|null
     */
    protected ?array $groups;

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
    protected string $sortBy = 'date';

    /**
     * @var boolean
     */
    protected bool $descending = false;

    /**
     * @var array|null
     */
    protected ?array $branchUsers;

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
            'users'         => $request->get('users'),
            'groups'        => $request->get('groups'),
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
            ->forUsers($settings['users'])
            ->forTeams($settings['teams'])
            ->forGroups($settings['groups'])
            ->forBranches($settings['branches'])
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
            'date'             => 'date',
            'accounts'         => 'accounts',
            'active'           => 'active',
            'banned'           => 'banned',
            'banned_percent'   => 'banned%',
            'approved'         => 'approved',
            'disapproved'      => 'disapproved',
            'approved_percent' => 'approved%',
            'spend'            => 'spend',
            'avg_spend'        => 'avg.spend',
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
                'date'              => Carbon::parse($item->date)->toDateString(),
                'accounts'          => $item->accounts ?? 0,
                'active'            => $item->active ?? 0,
                'banned'            => $item->banned ?? 0,
                'banned_percent'    => sprintf('%s %%', percentage($item->banned, $item->accounts)),
                'approved'          => $item->approved ?? 0,
                'disapproved'       => $item->disapproved ?? 0,
                'approved_percent'  => sprintf('%s %%', percentage($item->approved, $item->accounts)),
                'spend'             => $item->spend ?? 0,
                'avg_spend'         => sprintf('%s $', $item->accounts > 0 ? round($item->spend / $item->accounts, 2) : 0),
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
            'date'              => 'Итого',
            'accounts'          => $data->sum('accounts') ?? 0,
            'active'            => $data->sum('active') ?? 0,
            'banned'            => $data->sum('banned') ?? 0,
            'banned_percent'    => sprintf('%s %%', percentage($data->sum('banned'), $data->sum('accounts'))),
            'approved'          => $data->sum('approved') ?? 0,
            'disapproved'       => $data->sum('disapproved') ?? 0,
            'approved_percent'  => sprintf('%s %%', percentage($data->sum('approved'), $data->sum('accounts'))),
            'spend'             => $data->sum('spend') ?? 0,
            'avg_spend'         => sprintf(
                '%s $',
                $data->sum('accounts') > 0 ? round($data->sum('spend') / $data->sum('accounts'), 2) : 0
            ),
        ];
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return ManualPour::query()
            ->select([
                'manual_pours.date',
                DB::raw('count(pivot.account_id) as accounts'),
                DB::raw('count(CASE WHEN pivot.status=\'active\' THEN 1 END) as active'),
                DB::raw('count(CASE WHEN pivot.status=\'blocked\' THEN 1 END) as banned'),
                DB::raw('count(CASE WHEN pivot.moderation_status=\'approved\' THEN 1 END) as approved'),
                DB::raw('count(CASE WHEN pivot.moderation_status=\'disapproved\' THEN 1 END) as disapproved'),
                DB::raw('sum(insights.spend) as spend'),
            ])
            ->join('manual_account_manual_pour as pivot', 'manual_pours.id', 'pivot.pour_id')
            ->leftJoinSub(
                $this->insights(),
                'insights',
                function (JoinClause $join) {
                    $join->on('pivot.account_id', 'insights.account_id')
                        ->on('manual_pours.date', 'insights.date');
                }
            )
            ->whereBetween('manual_pours.date', [$this->since, $this->until])
            ->when($this->users, fn (Builder $builder) => $builder->whereIn('manual_pours.user_id', $this->users))
            ->when($this->groups, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'pivot.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->branchUsers, fn (Builder $builder) => $builder->whereIn('manual_pours.user_id', $this->branchUsers))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'manual_pours.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->verticalOffers, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_campaigns')
                        ->join('manual_bundles', 'manual_campaigns.bundle_id', 'manual_bundles.id')
                        ->whereColumn('manual_campaigns.account_id', '=', 'pivot.account_id')
                        ->whereIn('manual_bundles.offer_id', $this->verticalOffers);
                });
            })
            ->groupBy(['manual_pours.date'])
            ->get();
    }

    /**
     * @return ManualInsight|Builder|\Illuminate\Database\Query\Builder
     */
    protected function insights()
    {
        return ManualInsight::allowedOffers()
            ->select([
                'date',
                'account_id',
                DB::raw('sum(spend) as spend'),
            ])
            ->whereBetween('date', [$this->since, $this->until])
            ->when($this->verticalOffers, function (Builder $builder) {
                return $builder->whereHas('campaign.bundle', fn ($q) => $q->whereIn('manual_bundles.offer_id', $this->verticalOffers));
            })
            ->groupBy(['date', 'account_id']);
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
     * Filter by users
     *
     * @param null|array $users
     *
     * @return $this
     */
    public function forUsers($users = null)
    {
        $this->users = auth()->user()->isBuyer()
            ? auth()->user()->sharedUsers()->pluck('users.id')->push(auth()->id())->toArray()
            : $users;

        return $this;
    }

    /**
     * Filter by account groups
     *
     * @param null|array $groups
     *
     * @return $this
     */
    public function forGroups($groups = null)
    {
        $this->groups = $groups;

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
}
