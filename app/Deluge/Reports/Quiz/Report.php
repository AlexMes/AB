<?php

namespace App\Deluge\Reports\Quiz;

use App\Deposit;
use App\Lead;
use App\LeadOrderAssignment;
use App\ManualBundle;
use App\ManualInsight;
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
    public const OFFER          = 'offer';
    public const SOURCE         = 'utm_source';
    public const CAMPAIGN       = 'utm_campaign';
    public const CONTENT        = 'utm_content';
    public const USER           = 'user';
    public const TRAFFIC_SOURCE = 'traffic_source';

    public const GROUP_BY = [self::OFFER, self::SOURCE, self::CAMPAIGN, self::CONTENT, self::USER, self::TRAFFIC_SOURCE];

    public const PART_FB = 'fb';
    public const PART_TT = 'tt';
    public const PART_VK = 'vk';
    public const PART_UT = 'ut';
    public const PART_KD = 'kd';
    public const PARTS   = [
        self::PART_FB,
        self::PART_TT,
        self::PART_VK,
        self::PART_UT,
        self::PART_KD,
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
     * @var string
     */
    protected string $groupBy;

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
    protected ?array $branchUsers;

    /**
     * @var array|null
     */
    protected ?array $teams;

    /**
     * @var array|null
     */
    protected ?array $bundleOffers = null;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var string|null
     */
    protected ?string $part;

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
            'since'      => $request->get('since'),
            'until'      => $request->get('until'),
            'group'      => $request->get('group'),
            'offers'     => $request->get('offers'),
            'users'      => $request->get('users'),
            'teams'      => $request->get('teams'),
            'branches'   => $request->get('branches'),
            'bundles'    => $request->get('bundles'),
            'vertical'   => $request->get('vertical'),
            'part'       => $request->get('part'),
            'sort_by'    => $request->get('sort'),
            'descending' => $request->boolean('desc'),
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
            ->forBranches($settings['branches'])
            ->forTeams($settings['teams'])
            ->forBundles($settings['bundles'])
            ->forVertical($settings['vertical'])
            ->forPart($settings['part'])
            ->groupBy($settings['group'] ?? self::OFFER);

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
        return collect([
            'name'        => 'name',
            'leads'       => 'leads',
            'ftd'         => 'ftd',
            'ftd_percent' => 'ftd%',
            'spend'       => 'spend',
            'cpl'         => 'cpl',
            'benefit'     => 'revenue',
        ])->reject(function ($value, $key) {
            if (!$this->shouldAddBenefit() && $key === 'benefit') {
                return true;
            }

            return false;
        })->toArray();
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
                'name'        => $item->fcname ?? '',
                'leads'       => $item->leads ?? 0,
                'ftd'         => $item->ftd ?? 0,
                'ftd_percent' => sprintf('%s %%', percentage($item->ftd, $item->leads)),
                'spend'       => round($item->spend ?? 0, 2),
                'cpl'         => round(zero_safe_division($item->spend, $item->leads), 2),
                'benefit'     => $this->shouldAddBenefit() ? $item->leads_benefit + $item->deps_benefit : 0,
            ])->reject(function ($value, $key) {
                if (!$this->shouldAddBenefit() && $key === 'benefit') {
                    return true;
                }

                return false;
            })->toArray();
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
            'name'        => 'Итого',
            'leads'       => $data->sum('leads'),
            'ftd'         => $data->sum('ftd'),
            'ftd_percent' => sprintf('%s %%', percentage($data->sum('ftd'), $data->sum('leads'))),
            'spend'       => $data->sum('spend'),
            'cpl'         => round(zero_safe_division($data->sum('spend'), $data->sum('leads')), 2),
            'benefit'     => $this->shouldAddBenefit() ? $data->sum(fn ($item) => $item->leads_benefit + $item->deps_benefit) : 0,
        ])->reject(function ($value, $key) {
            if (!$this->shouldAddBenefit() && $key === 'benefit') {
                return true;
            }

            return false;
        })->toArray();
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        $query = Lead::visible()
            ->allowedOffers()
            ->allowedBranches()
            ->when(
                $this->shouldAddBenefit(),
                function (Builder $builder) {
                    return $builder->select([
                        DB::raw('count(DISTINCT leads.id) as leads'),
                        DB::raw('sum(deposits.cnt) as ftd'),
                        DB::raw('sum(deposits.benefit) as deps_benefit'),
                        DB::raw('sum(assignments.benefit) as leads_benefit'),

                        DB::raw('ROUND(sum(costs.spend) / count(leads.id), 2) as spend'),
                    ])
                        ->leftJoinSub($this->deposits(), 'deposits', 'deposits.lead_id', 'leads.id')
                        ->leftJoinSub($this->assignments(), 'assignments', 'assignments.lead_id', 'leads.id');
                },
                function (Builder $builder) {
                    return $builder->select([
                        DB::raw('count(DISTINCT leads.id) as leads'),
                        DB::raw('count(DISTINCT deposits.id) as ftd'),
                        DB::raw('ROUND(sum(costs.spend) / count(leads.id), 2) as spend')
                    ])
                        ->leftJoin(
                            'deposits',
                            'leads.id',
                            '=',
                            'deposits.lead_id'
                        );
                },
            )
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->valid()
            ->own()
            ->when($this->groupBy === self::OFFER, function (Builder $builder) {
                return $builder->addSelect(DB::raw("replace(offers.name, 'LO_', '') as fcname"))
                    ->leftJoin('offers', 'leads.offer_id', 'offers.id');
            })
            ->when($this->groupBy === self::SOURCE, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_source as fcname'));
            })
            ->when($this->groupBy === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_campaign as fcname'));
            })
            ->when($this->groupBy === self::CONTENT, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_content as fcname'));
            })
            ->when($this->groupBy === self::USER, function (Builder $builder) {
                return $builder->addSelect(DB::raw('users.name as fcname'));
            })
            ->when($this->groupBy === self::TRAFFIC_SOURCE, function (Builder $builder) {
                return $builder->selectRaw(
                    "CASE
                        WHEN manual_bundles.traffic_source_id is null THEN 'none'
                        WHEN manual_bundles.traffic_source_id is not null THEN manual_traffic_sources.name
                    END as fcname"
                )
                    ->leftJoin('offers', 'leads.offer_id', 'offers.id')
                    ->leftJoin('manual_bundles', 'offers.id', 'manual_bundles.offer_id')
                    ->leftJoin('manual_traffic_sources', 'manual_bundles.traffic_source_id', 'manual_traffic_sources.id');
            })
            ->leftJoinSub($this->costs(), 'costs', 'costs.groupname', '=', $this->grouper())
            ->whereBetween('leads.created_at', [
                $this->since->startOfDay()->toDateTimeString(),
                $this->until->endOfDay()->toDateTimeString()
            ])
            ->when($this->users, fn (Builder $builder) => $builder->whereIn('leads.user_id', $this->users))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('leads.offer_id', $this->offers))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->branchUsers, fn (Builder $builder) => $builder->whereIn('leads.user_id', $this->branchUsers))
            ->when($this->bundleOffers, fn (Builder $builder) => $builder->whereIn('leads.utm_campaign', $this->bundleOffers))
            ->when($this->verticalOffers, fn (Builder $builder) => $builder->whereIn('leads.offer_id', $this->verticalOffers))
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            })
            ->groupByRaw('fcname');

        return collect(DB::select($query->toSql(), $query->getBindings()));
    }

    /**
     * @return LeadOrderAssignment|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    public function assignments()
    {
        return LeadOrderAssignment::visible()
            ->select([
                'lead_id',
                DB::raw('sum(lead_order_assignments.benefit) as benefit'),
            ])
            // dont count resell for buyers
            ->when(auth()->user()->isBuyer(), fn ($query) => $query->whereNotExists(function ($q) {
                return $q->select(DB::raw(1))
                    ->from('lead_resell_batch')
                    ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id');
            }))
            ->groupBy('lead_id');
    }

    /**
     * @return Deposit|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    public function deposits()
    {
        return Deposit::visible()
            ->select([
                'deposits.lead_id',
                DB::raw('count(deposits.id) as cnt'),
                DB::raw('sum(deposits.benefit) as benefit'),
            ])
            // dont count deps from resell for buyers
            ->when(auth()->user()->isBuyer(), function ($query) {
                return $query->leftJoin('lead_order_assignments', function (JoinClause $joinClause) {
                    return $joinClause->on('deposits.lead_id', 'lead_order_assignments.lead_id')
                        ->on('deposits.lead_return_date', DB::raw('lead_order_assignments.created_at::date'));
                })->whereNotExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('lead_resell_batch')
                        ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id');
                });
            })
            ->when($this->users, fn (Builder $builder) => $builder->whereIn('deposits.user_id', $this->users))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('deposits.offer_id', $this->offers))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'deposits.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->branchUsers, fn (Builder $builder) => $builder->whereIn('deposits.user_id', $this->branchUsers))
            ->when($this->bundleOffers, fn (Builder $builder) => $builder->whereHas('lead', fn ($query) => $query->whereIn('utm_campaign', $this->bundleOffers)))
            ->when($this->verticalOffers, fn (Builder $builder) => $builder->whereIn('deposits.offer_id', $this->verticalOffers))
            ->when($this->part, function (Builder $builder) {
                return $builder->whereHas('lead', function ($query) {
                    return $query->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            })
            ->orderBy('deposits.lead_id')
            ->groupBy('deposits.lead_id');
    }

    /**
     * Get name for group
     *
     * @return string
     */
    public function grouper()
    {
        switch ($this->groupBy) {
            case self::OFFER:
                return "offers.name";

                break;
            case self::SOURCE:
                return 'leads.utm_source';

                break;
            case self::CAMPAIGN:
                return 'leads.utm_campaign';

                break;
            case self::CONTENT:
                return 'leads.utm_content';

                break;
            case self::USER:
                return 'users.name';

                break;
            case self::TRAFFIC_SOURCE:
                return 'manual_traffic_sources.name';

                break;
            default:
                return "offers.name";

                break;
        }
    }

    /**
     * Get costs for leads
     *
     * @return void
     */
    public function costs()
    {
        return ManualInsight::selectRaw('sum(spend::decimal) as spend')
            ->whereBetween('date', [$this->since->toDateString(),$this->until->toDateString()])
            /*->visible()*/
            ->when(
                auth()->id() === 230,
                fn ($query) => $query->where('manual_insights.date', '<', '2021-11-05 00:00:00')
                    ->where('manual_accounts.created_at', '<', '2021-11-05 00:00:00'),
                fn ($query) => $query->unless(auth()->user()->isAdmin(), function ($q) {
                    return $q->where('manual_accounts.created_at', '>', now()->subYear()->toDateTimeString())
                        ->whereIn('manual_accounts.user_id', $this->users ?? User::visible()->pluck('id'));
                })
            )

            ->join('manual_campaigns', 'manual_insights.campaign_id', '=', 'manual_campaigns.id')
            ->join('manual_bundles', 'manual_campaigns.bundle_id', '=', 'manual_bundles.id')
            ->join('offers', 'manual_bundles.offer_id', '=', 'offers.id')
            ->join('manual_accounts', 'manual_insights.account_id', '=', 'manual_accounts.account_id')
            ->leftJoin('users', 'manual_accounts.user_id', '=', 'users.id')
            ->when($this->users, fn (Builder $builder) => $builder->whereIn('manual_accounts.user_id', $this->users))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('offers.id', $this->offers))
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'manual_accounts.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->branchUsers, fn (Builder $builder) => $builder->whereIn('manual_accounts.user_id', $this->branchUsers))
            ->when($this->bundleOffers, fn (Builder $builder) => $builder->whereIn('manual_campaigns.utm_key', $this->bundleOffers))
            ->when($this->verticalOffers, fn (Builder $builder) => $builder->whereIn('offers.id', $this->verticalOffers))
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('manual_campaigns.utm_key', 'like', "%-{$this->part}%")
                        ->orWhere('manual_campaigns.utm_key', '=', $this->part);
                });
            })
            ->when($this->groupBy === self::OFFER, function (Builder $builder) {
                return $builder->addSelect(DB::raw('offers.name as groupname'));
            })
            ->when($this->groupBy === self::SOURCE, function (Builder $builder) {
                return $builder->addSelect(DB::raw('manual_campaigns.name as groupname'));
            })
            ->when($this->groupBy === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect(DB::raw('manual_campaigns.utm_key as groupname'));
            })
            ->when($this->groupBy === self::CONTENT, function (Builder $builder) {
                return $builder->addSelect(DB::raw('manual_campaigns.creo as groupname'));
            })
            ->when($this->groupBy === self::TRAFFIC_SOURCE, function (Builder $builder) {
                return $builder->selectRaw(
                    "CASE
                        WHEN manual_bundles.traffic_source_id is null THEN 'none'
                        WHEN manual_bundles.traffic_source_id is not null THEN manual_traffic_sources.name
                    END as groupname"
                )
                    ->leftjoin('manual_traffic_sources', 'manual_bundles.traffic_source_id', '=', 'manual_traffic_sources.id');
            })
            ->when($this->groupBy === self::USER, function (Builder $builder) {
                return $builder->addSelect(DB::raw('users.name as groupname'));
            })->groupBy('groupname');
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
        $this->since = Carbon::parse($since);

        if (auth()->user()->isDesigner()) {
            $this->since = $this->since->max(now()->subMonths(2)->startOfMonth());
        }

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
        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function groupBy(string $group): Report
    {
        $this->groupBy = in_array($group, self::GROUP_BY) ? $group : self::OFFER;

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param null|array $offers
     *
     * @return $this
     */
    public function forOffers($offers = null): Report
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
    public function forUsers($users = null): Report
    {
        if ($users !== null) {
            $this->users = User::visible()->pluck('id')->intersect($users)->values()->push(0)->toArray();

            return $this;
        }

        $this->users = null;

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
     * Filters by branches
     *
     * @param array|null $branches
     *
     * @return $this
     */
    public function forBranches(?array $branches = null)
    {
        if ($branches !== null) {
            $this->branchUsers = User::visible()
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
     * Filter by bundles
     *
     * @param null|array $bundles
     *
     * @return $this
     */
    public function forBundles($bundles = null)
    {
        if (!empty($bundles)) {
            $this->bundleOffers = ManualBundle::query()
                ->whereIn('id', $bundles)
                ->pluck('name')
                ->toArray();
        } else {
            $this->bundleOffers = null;
        }

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
     * @param string|null $part
     *
     * @return $this
     */
    public function forPart(?string $part = null)
    {
        $this->part = in_array($part, self::PARTS) ? $part : null;

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

    protected function shouldAddBenefit()
    {
        if (
            auth()->user()->isBuyer() && !in_array(auth()->user()->name, ['SHMN', 'BRIT', 'MALOY'])
            && auth()->user()->branch_id !== 25
        ) {
            return false;
        }

        return auth()->user()->displayRevenue()
            && in_array(auth()->user()->branch_id, [16, 25]) || auth()->id() === 1;
    }
}
