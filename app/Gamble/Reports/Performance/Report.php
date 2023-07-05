<?php

namespace App\Gamble\Reports\Performance;

use App\Gamble\Insight;
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
     * Offer filter
     *
     * @var array|null
     */
    protected ?array $offers;

    /**
     * @var array|null
     */
    protected ?array $users;

    /**
     * @var array|null
     */
    protected ?array $accounts;

    /**
     * @var array|null
     */
    protected ?array $groups;

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
            'offers'        => $request->get('offers'),
            'users'         => $request->get('users'),
            'accounts'      => $request->get('accounts'),
            'groups'        => $request->get('groups'),
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
            ->forOffers($settings['offers'])
            ->forUsers($settings['users'])
            ->forAccounts($settings['accounts'])
            ->forGroups($settings['groups']);
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

    protected function headers(): array
    {
        return collect([
            'date'              => 'date',
            'buyer'             => 'buyer',
            'spend'             => 'spend',
            'tech_spend'        => 'tech',
            'app'               => 'app',
            'installs'          => 'inst',
            'cpi'               => 'cpi',
            'leads'             => 'leads',
            'cpl'               => 'cpl',
            'ftd'               => 'ftd',
            'installs_to_leads' => 'inst2reg',
            'leads_to_ftd'      => 'reg2dep',
            'cpd'               => 'cpd',
            'rev'               => 'rev',
            'profit'            => 'profit',
            'roi'               => 'roi',
        ])->toArray();
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
            $totalSpend = $item->spend + $item->tech_spend + $item->app;

            return collect([
                'date'              => $item->date,
                'buyer'             => $item->buyer,
                'spend'             => sprintf('%s $', $item->spend),
                'tech_spend'        => sprintf('%s $', $item->tech_spend),
                'app'               => sprintf('%s $', round($item->app, 2)),
                'installs'          => $item->installs,
                'cpi'               => sprintf(
                    '%s $',
                    $item->installs > 0 ? round($item->spend / $item->installs, 2) : 0
                ),
                'leads'             => $item->registrations,
                'cpl'               => sprintf(
                    '%s $',
                    $item->registrations > 0 ? round($item->spend / $item->registrations, 2) : 0
                ),
                'ftd'               => $item->deposit_cnt,
                'installs_to_leads' => sprintf('%s %%', percentage($item->registrations, $item->installs)),
                'leads_to_ftd'      => sprintf('%s %%', percentage($item->deposit_cnt, $item->registrations)),
                'cpd'               => sprintf(
                    '%s $',
                    $item->deposit_cnt > 0 ? round($totalSpend / $item->deposit_cnt, 2) : 0
                ),
                'rev'               => sprintf('%s $', $item->revenue),
                'profit'            => sprintf('%s $', $item->revenue - $totalSpend),
                'roi'               => sprintf('%s %%', percentage($item->revenue - $totalSpend, $totalSpend)),
            ])->toArray();
        });
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
        $totalSpend = $data->sum('spend') + $data->sum('tech_spend') + $data->sum('app');

        return collect([
            'date'              => '',
            'buyer'             => 'Total',
            'spend'             => sprintf('%s $', $data->sum('spend')),
            'tech_spend'        => sprintf('%s $', $data->sum('tech_spend')),
            'app'               => sprintf('%s $', round($data->sum('app'), 2)),
            'installs'          => $data->sum('installs'),
            'cpi'               => sprintf(
                '%s $',
                $data->sum('installs') > 0
                    ? round($data->sum('spend') / $data->sum('installs'), 2)
                    : 0
            ),
            'leads'             => $data->sum('registrations'),
            'cpl'               => sprintf(
                '%s $',
                $data->sum('registrations') > 0
                    ? round($data->sum('spend') / $data->sum('registrations'), 2)
                    : 0
            ),
            'ftd'               => $data->sum('deposit_cnt'),
            'installs_to_leads' => sprintf('%s %%', percentage($data->sum('registrations'), $data->sum('installs'))),
            'leads_to_ftd'      => sprintf('%s %%', percentage($data->sum('deposit_cnt'), $data->sum('registrations'))),
            'cpd'               => sprintf(
                '%s $',
                $data->sum('deposit_cnt') > 0 ? round($totalSpend / $data->sum('deposit_cnt'), 2) : 0
            ),
            'rev'               => sprintf('%s $', $data->sum('revenue')),
            'profit'            => sprintf('%s $', $data->sum('revenue') - $totalSpend),
            'roi'               => sprintf('%s %%', percentage($data->sum('revenue') - $totalSpend, $totalSpend)),
        ])->toArray();
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return Insight::visible()
            ->select([
                'gamble_insights.date',
                'users.name as buyer',
                DB::raw('sum(gamble_insights.spend) as spend'),
                DB::raw('coalesce(gamble_tech_costs.spend, 0) as tech_spend'),
                DB::raw('sum(gamble_insights.installs) * 0.1 as app'),
                DB::raw('sum(gamble_insights.installs) as installs'),
                DB::raw('sum(gamble_insights.registrations) as registrations'),
                DB::raw('sum(gamble_insights.deposit_cnt) as deposit_cnt'),
                DB::raw('sum(gamble_insights.deposit_cnt * gamble_insights.deposit_sum) as revenue'),
            ])
            ->join('gamble_accounts', 'gamble_insights.account_id', 'gamble_accounts.id')
            ->leftJoin('users', 'gamble_accounts.user_id', 'users.id')
            ->leftJoin('gamble_tech_costs', function (JoinClause $join) {
                return $join->on('gamble_insights.date', 'gamble_tech_costs.date')
                    ->on('users.id', 'gamble_tech_costs.user_id');
            })
            ->whereBetween('gamble_insights.date', [$this->since, $this->until])
            ->notEmptyWhereIn('gamble_accounts.user_id', $this->users)
            ->when($this->offers, function (Builder $builder) {
                return $builder->join('gamble_campaigns', 'gamble_insights.campaign_id', 'gamble_campaigns.id')
                    ->whereIn('gamble_campaigns.offer_id', $this->offers);
            })
            ->notEmptyWhereIn('gamble_insights.account_id', $this->accounts)
            ->when($this->groups, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('gamble_account_gamble_group')
                        ->whereColumn('gamble_account_gamble_group.account_id', 'gamble_insights.account_id')
                        ->whereIn('gamble_account_gamble_group.group_id', $this->groups);
                });
            })
            ->groupBy('gamble_insights.date', 'users.name', 'tech_spend')
            ->orderBy('gamble_insights.date')
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
    public function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until)->endOfDay();

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param null|array $offers
     *
     * @return $this
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

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
        $this->users = $users;

        return $this;
    }

    /**
     * Filter by accounts
     *
     * @param null|array $accounts
     *
     * @return $this
     */
    public function forAccounts($accounts = null)
    {
        $this->accounts = $accounts;

        return $this;
    }

    /**
     * @param array|null $groups
     *
     * @return $this
     */
    public function forGroups(?array $groups = null)
    {
        $this->groups = $groups;

        return $this;
    }
}
