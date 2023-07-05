<?php

namespace App\Deluge\Reports\AverageSpend;

use App\ManualInsight;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
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
     * Offer filter
     *
     * @var array|null
     */
    protected ?array $branches = null;

    /**
     * @var array|null
     */
    protected ?array $teams = null;

    /**
     * @var array|null
     */
    protected ?array $groups = null;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var array|null
     */
    protected ?array $suppliers = null;

    /**
     * @var string
     */
    protected string $sortBy = 'buyer';

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
            'groups'        => $request->get('groups'),
            'vertical'      => $request->get('vertical'),
            'suppliers'     => $request->get('suppliers'),
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
            ->forGroups($settings['groups'])
            ->forVertical($settings['vertical'])
            ->forSuppliers($settings['suppliers']);

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
            'buyer'          => 'buyer',
            'accounts_count' => 'accounts',
            'spend'          => 'spend',
            'avg_spend'      => 'avg spend'
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
                'buyer'          => $item->buyer ?? '',
                'accounts_count' => $item->accounts_count,
                'spend'          => $item->spend,
                'avg_spend'      => round($item->spend / $item->accounts_count, 2),
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
            'buyer'          => 'Итого',
            'accounts_count' => $data->sum('accounts_count'),
            'spend'          => $data->sum('spend'),
            'avg_spend'      => $data->sum('accounts_count') > 0
                ? round($data->sum('spend') / $data->sum('accounts_count'), 2)
                : 0,
        ];
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return ManualInsight::visible()->allowedOffers()
            ->select([
                DB::raw('users.name as buyer'),
                DB::raw('count(distinct manual_insights.account_id) as accounts_count'),
                DB::raw('sum(manual_insights.spend) as spend'),
            ])
            ->join('manual_accounts', 'manual_insights.account_id', 'manual_accounts.account_id')
            ->leftJoin('users', 'manual_accounts.user_id', 'users.id')
            ->when($this->since, fn ($query) => $query->whereDate('manual_insights.date', '>=', $this->since))
            ->when($this->until, fn ($query) => $query->whereDate('manual_insights.date', '<=', $this->until))
            ->when($this->branches, fn ($query) => $query->whereIn('users.branch_id', $this->branches))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function ($query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'manual_accounts.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->groups, function (Builder $builder) {
                return $builder->whereExists(function ($query) {
                    return $query->selectRaw(1)
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_insights.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->verticalOffers, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->whereHas('bundle', fn ($q1) => $q1->whereIn('offer_id', $this->verticalOffers));
                });
            })
            ->when($this->suppliers, fn ($query) => $query->whereIn('manual_accounts.supplier_id', $this->suppliers))
            ->groupBy(['buyer'])
            ->get();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return $this
     */
    public function since($since = null): Report
    {
        $this->since = Carbon::parse($since)->startOfDay();

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return $this
     */
    public function until($until = null): Report
    {
        $this->until = Carbon::parse($until)->endOfDay();

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param null|array $branches
     *
     * @return $this
     */
    public function forBranches($branches = null): Report
    {
        $this->branches = $branches;

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
     * Filter by bundles
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
     * Filter by suppliers
     *
     * @param null|array $suppliers
     *
     * @return $this
     */
    public function forSuppliers($suppliers = null)
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    /**
     * @param string|null $sortBy
     * @param bool|null   $descending
     *
     * @return $this
     */
    public function sortBy(?string $sortBy, ?bool $descending = false): Report
    {
        if ($sortBy !== null && array_key_exists($sortBy, $this->headers())) {
            $this->sortBy = $sortBy;
        }

        $this->descending = $descending ?? false;

        return $this;
    }
}
