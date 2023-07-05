<?php

namespace App\Deluge\Reports\GroupStats;

use App\Deposit;
use App\ManualAccount;
use App\ManualAccountManualPour;
use App\ManualInsight;
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
    protected ?array $groups;

    /**
     * @var array
     */
    protected array $hideColumns;

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
            'groups'        => $request->get('groups'),
            'sort_by'       => $request->get('sort'),
            'descending'    => $request->boolean('desc'),
            'hide_cols'     => $request->get('hide_cols'),
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
            ->forGroups($settings['groups'] ?? null)
            ->hideColumns($settings['hide_cols'] ?? null);

        $this->sortBy($settings['sort_by'] ?? null, $settings['descending'] ?? null);
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
        return collect([
            'name'                  => 'Группа',
            'moderation_approved'   => 'Прохождение модерации %',
            'avg_spend'             => 'Средний спенд $',
            'blocked'               => 'Бан %',
            'avg_lifetime'          => 'Среднее время жизни, минут',
        ])->reject(fn ($value, $key) => in_array($key, $this->hideColumns))
            ->toArray();
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
            return collect([
                'name'                  => $item->name ?? '',
                'moderation_approved'   => sprintf('%s %%', percentage($item->approved, $item->pours_cnt)),
                'avg_spend'             => $item->accounts > 0 ?
                    sprintf('%s $', round($item->spend / $item->accounts, 2))
                    : 0,
                'blocked'               => sprintf('%s %%', percentage($item->blocked, $item->pours_cnt)),
                'avg_lifetime'          => $item->accounts > 0 ? round($item->lifetime / $item->accounts, 2) : 0,
            ])->reject(fn ($value, $key) => in_array($key, $this->hideColumns))
                ->toArray();
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
        return collect([
            'name'                  => 'Итого',
            'moderation_approved'   => sprintf('%s %%', percentage($data->sum('approved'), $data->sum('pours_cnt'))),
            'avg_spend'             => $data->sum('accounts') > 0 ?
                sprintf('%s $', round($data->sum('spend') / $data->sum('accounts'), 2))
                : 0,
            'blocked'               => sprintf('%s %%', percentage($data->sum('blocked'), $data->sum('pours_cnt'))),
            'avg_lifetime'          => $data->sum('accounts') > 0 ?
                round($data->sum('lifetime') / $data->sum('accounts'), 2)
                : 0,
        ])->reject(fn ($value, $key) => in_array($key, $this->hideColumns))
            ->toArray();
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return ManualAccount::query()
            ->select([
                'manual_groups.name',
                DB::raw('count(manual_accounts.account_id) as accounts'),
                DB::raw('COALESCE(sum(insights.spend), 0) as spend'),
                DB::raw('COALESCE(sum(pours.cnt), 0) as pours_cnt'),
                DB::raw('COALESCE(sum(pours.approved), 0) as approved'),
                DB::raw('COALESCE(sum(pours.blocked), 0) as blocked'),
                DB::raw('COALESCE(sum(pours.lifetime), 0) as lifetime'),
            ])
            // if $this->groups.count != 1 it won't work correctly
            // but this report was created only for group page, so it's fine.
            ->join('manual_account_manual_group', function (JoinClause $join) {
                return $join->on('manual_accounts.account_id', 'manual_account_manual_group.account_id')
                    ->when($this->groups, fn ($q) => $q->whereIn('manual_account_manual_group.group_id', $this->groups));
            })
            ->join('manual_groups', 'manual_account_manual_group.group_id', 'manual_groups.id')
            ->leftJoinSub($this->insights(), 'insights', 'manual_accounts.account_id', 'insights.account_id')
            ->leftJoinSub($this->pours(), 'pours', 'manual_accounts.account_id', 'pours.account_id')
            ->groupBy('manual_groups.name')
            ->get();
    }

    /**
     * @return Deposit|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    public function insights()
    {
        return ManualInsight::allowedOffers()
            ->select([
                'account_id',
                DB::raw('sum(spend) as spend'),
            ])
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_insights.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->groupBy('account_id');
    }

    /**
     * @return ManualAccountManualPour|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    public function pours()
    {
        return ManualAccountManualPour::query()
            ->select([
                'manual_account_manual_pour.account_id',
                DB::raw('count(*) as cnt'),
                DB::raw('count(CASE WHEN manual_account_manual_pour.moderation_status=\'approved\' THEN 1 END)::decimal as approved'),
                DB::raw('count(CASE WHEN manual_account_manual_pour.status=\'blocked\' THEN 1 END)::decimal as blocked'),
                DB::raw('EXTRACT(EPOCH FROM age(
                        max(CASE WHEN manual_account_manual_pour.status=\'active\' THEN manual_pours.date END),
                        GREATEST(max(manual_accounts.created_at::date), \'' . $this->since->toDateString() . '\')
                    ))::int / 60 as lifetime'),
            ])
            ->join('manual_pours', 'manual_account_manual_pour.pour_id', 'manual_pours.id')
            ->join('manual_accounts', 'manual_account_manual_pour.account_id', 'manual_accounts.account_id')
            ->whereBetween('manual_pours.date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_accounts.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->groupBy('manual_account_manual_pour.account_id');
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
     * Filter by groups
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
     * @param array|null $columns
     */
    public function hideColumns(array $columns = null)
    {
        $this->hideColumns = $columns ?? [];
    }
}
