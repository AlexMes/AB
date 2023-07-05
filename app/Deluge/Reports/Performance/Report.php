<?php

namespace App\Deluge\Reports\Performance;

use App\Binom\Statistic;
use App\Deposit;
use App\Lead;
use App\ManualAccount;
use App\ManualCampaign;
use App\ManualInsight;
use App\Offer;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Report implements Arrayable, Responsable
{
    public const ACCOUNT = 'account';

    public const CAMPAIGN = 'campaign';

    public const CREO = 'creo';

    public const LEVELS = [self::ACCOUNT, self::CAMPAIGN, self::CREO];

    public const DESIGNER_COLUMNS = ['name', 'cpm', 'cpc', 'ctr', 'board_leads', 'ftd', 'ftd_percent', 'fb_cpl'];

    public const BRANCH_19_COLUMNS = ['name', 'cpm', 'cpc', 'ctr', 'leads', 'ftd', 'ftd_percent', 'fb_cpl', 'cost'];

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
    protected string $level;

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
    protected ?array $teams;

    /**
     * @var array|null
     */
    protected ?array $accounts;

    /**
     * @var array|null
     */
    protected ?array $utmCampaigns;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $campaignOffers;

    /**
     * @var array|null
     */
    protected ?array $groups;

    /**
     * @var array|null
     */
    protected ?array $branchUsers;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var string|null
     */
    protected ?string $part;

    /**
     * @var bool
     */
    protected bool $groupByUser;

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
            'level'         => $request->get('level'),
            'offers'        => $request->get('offers'),
            'users'         => $request->get('users'),
            'teams'         => $request->get('teams'),
            'accounts'      => $request->get('accounts'),
            'utm_campaigns' => $request->get('utm_campaigns'),
            'groups'        => $request->get('groups'),
            'branches'      => $request->get('branches'),
            'vertical'      => $request->get('vertical'),
            'part'          => $request->get('part'),
            'by_user'       => $request->get('by_user'),
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
            ->forLevel($settings['level'] ?? self::CAMPAIGN)
            ->forOffers($settings['offers'])
            ->forUsers($settings['users'])
            ->forTeams($settings['teams'])
            ->forAccounts($settings['accounts'])
            ->forUtmCampaigns($settings['utm_campaigns'])
            ->forGroups($settings['groups'])
            ->forBranches($settings['branches'])
            ->forVertical($settings['vertical'])
            ->forPart($settings['part'])
            ->groupByUser($settings['by_user'] ?? false);

        $this->loadCampaignOffers();
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
            'offer'       => 'offer',
            'buyer'       => 'buyer',
            'rk'          => 'РК',
            'impressions' => 'impressions',
            'clicks'      => 'clicks',
            'cpm'         => 'cpm',
            'cpc'         => 'cpc',
            'ctr'         => 'ctr',

            // 'bclicks'       => 'b.clicks',
            // 'lp_views'      => 'LP Views',
            // 'lp_clicks'     => 'LP Clicks',
            'lp_ctr'      => 'LP CTR',
            'offer_cr'    => 'Offer CR',
            'cr'          => 'CR',
            'board_leads' => 'board leads',
            'leads'       => 'leads',
            // 'bleads'        => 'b.leads',
            // 'bcost'         => 'b.cost',

            'ftd'         => 'ftd',
            'ftd_percent' => 'ftd%',
            'ftd_cost'    => 'ftd cost',
            // 'bftd_percent'  => 'b.ftd%',
            // 'rev'           => 'rev',
            'fb_cpl' => 'fb.cpl',
            // 'bcpl'          => 'b.cpl',
            'cost'    => 'cost',
            'profit'  => 'profit',
            'roi'     => 'roi',
            'benefit' => 'revenue',
        ])->reject(function ($value, $key) {
            if (!$this->groupByUser && $value === 'buyer') {
                return true;
            }
            if (!$this->shouldAddBenefit() && $key === 'benefit') {
                return true;
            }

            return false;
        })->when(auth()->user()->isDesigner(), function (Collection $collection) {
            return $collection->filter(function ($value, $key) {
                return in_array($key, self::DESIGNER_COLUMNS);
            });
        })->when(auth()->user()->branch_id === 19, function (Collection $collection) {
            return $collection->filter(function ($value, $key) {
                return in_array($key, self::BRANCH_19_COLUMNS);
            });
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
            $revenue = $item->deposits * 400 + $item->deposits_az * 300;

            return collect([
                'name'        => $item->name,
                'offer'       => optional($this->campaignOffers->get($item->name))->first()->bundle->offer->name ?? '',
                'buyer'       => $item->buyer ?? '',
                'rk'          => $item->accounts,
                'impressions' => $item->impressions,
                'clicks'      => $item->clicks,
                'cpm'         => round($item->impressions > 0 ? $item->spend / ($item->impressions / 1000) : 0, 2),
                'cpc'         => round($item->clicks > 0 ? $item->spend / $item->clicks : 0, 2),
                'ctr'         => percentage($item->clicks, $item->impressions),

                // 'bclicks'     => $item->bclicks,
                // 'lp_views'    => $item->lp_views,
                // 'lp_clicks'   => $item->lp_clicks,
                'lp_ctr'      => percentage($item->lp_clicks, $item->lp_views),
                'offer_cr'    => percentage($item->bleads, $item->lp_clicks),
                'cr'          => percentage($item->bleads, $item->bclicks),
                'board_leads' => $item->board_leads,
                'leads'       => $item->leads_cnt,
                // 'bleads'      => $item->bleads,
                // 'bcost'       => round($item->bcost ?? 0, 2),

                'ftd'         => $item->deposits_total,
                'ftd_percent' => percentage($item->deposits_total, $item->leads_cnt),
                'ftd_cost'    => round($item->deposits_total > 0 ? $item->spend / $item->deposits_total : 0, 2),
                // 'bftd_percent' => percentage($item->deposits_total, $item->bleads),
                // 'rev'          => $revenue,
                'fb_cpl' => round($item->leads_cnt > 0 ? $item->spend / $item->leads_cnt : 0, 2),
                // 'bcpl'         => round($item->bleads > 0 ? $item->spend / $item->bleads : 0, 2),
                'cost'    => $item->spend,
                'profit'  => $revenue - $item->spend,
                'roi'     => percentage($revenue - $item->spend, $item->spend),
                'benefit' => $item->leads_benefit + $item->deposits_benefit,
            ])->reject(function ($value, $key) {
                if (!$this->groupByUser && $key === 'buyer') {
                    return true;
                }
                if (!$this->shouldAddBenefit() && $key === 'benefit') {
                    return true;
                }

                return false;
            })->when(auth()->user()->isDesigner(), function (Collection $collection) {
                return $collection->filter(function ($value, $key) {
                    return in_array($key, self::DESIGNER_COLUMNS);
                });
            })->when(auth()->user()->branch_id === 19, function (Collection $collection) {
                return $collection->filter(function ($value, $key) {
                    return in_array($key, self::BRANCH_19_COLUMNS);
                });
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
        $revenue = $data->sum('deposits') * 400 + $data->sum('deposits_az') * 300;

        return collect([
            'name'        => 'Итого',
            'offer'       => '',
            'buyer'       => '',
            'rk'          => $data->sum('accounts'),
            'impressions' => $data->sum('impressions'),
            'clicks'      => $data->sum('clicks'),
            'cpm'         => round(
                $data->sum('impressions') > 0
                    ? $data->sum('spend') / ($data->sum('impressions') / 1000)
                    : 0,
                2
            ),
            'cpc' => round(
                $data->sum('clicks') > 0
                    ? $data->sum('spend') / $data->sum('clicks')
                    : 0,
                2
            ),
            'ctr' => percentage($data->sum('clicks'), $data->sum('impressions')),

            // 'bclicks'     => $data->sum('bclicks'),
            // 'lp_views'    => $data->sum('lp_views'),
            // 'lp_clicks'   => $data->sum('lp_clicks'),
            'lp_ctr'      => percentage($data->sum('lp_clicks'), $data->sum('lp_views')),
            'offer_cr'    => percentage($data->sum('bleads'), $data->sum('lp_clicks')),
            'cr'          => percentage($data->sum('bleads'), $data->sum('bclicks')),
            'board_leads' => $data->sum('board_leads'),
            'leads'       => $data->sum('leads_cnt'),
            // 'bleads'      => $data->sum('bleads'),
            // 'bcost'       => round($data->sum('bcost') ?? 0, 2),

            'ftd'         => $data->sum('deposits_total'),
            'ftd_percent' => percentage($data->sum('deposits_total'), $data->sum('leads_cnt')),
            'ftd_cost'    => round($data->sum('deposits_total') > 0 ? $data->sum('spend') / $data->sum('deposits_total') : 0, 2),
            // 'bftd_percent'  => percentage($data->sum('deposits_total'), $data->sum('bleads')),
            // 'rev'           => $revenue,
            'fb_cpl' => round(
                $data->sum('leads_cnt') > 0 ?
                    $data->sum('spend') / $data->sum('leads_cnt')
                    : 0,
                2
            ),
            // 'bcpl'        => round($data->sum('bleads') > 0 ? $data->sum('spend') / $data->sum('bleads') : 0, 2),
            'cost'    => $data->sum('spend'),
            'profit'  => $revenue - $data->sum('spend'),
            'roi'     => percentage($revenue - $data->sum('spend'), $data->sum('spend')),
            'benefit' => $data->sum(fn ($item) => $item->leads_benefit + $item->deposits_benefit),
        ])->reject(function ($value, $key) {
            if (!$this->groupByUser && $key === 'buyer') {
                return true;
            }
            if (!$this->shouldAddBenefit() && $key === 'benefit') {
                return true;
            }

            return false;
        })->when(auth()->user()->isDesigner(), function (Collection $collection) {
            return $collection->filter(function ($value, $key) {
                return in_array($key, self::DESIGNER_COLUMNS);
            });
        })->when(auth()->user()->branch_id === 19, function (Collection $collection) {
            return $collection->filter(function ($value, $key) {
                return in_array($key, self::BRANCH_19_COLUMNS);
            });
        })->toArray();
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
                DB::raw('count(distinct manual_insights.account_id) as accounts'),
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(clicks) as clicks'),
                DB::raw('sum(spend) as spend'),
                DB::raw('sum(leads_cnt) as leads_cnt'),
                DB::raw('sum(binom.bcost::decimal) as bcost'),
                DB::raw('COALESCE(binom.bclicks::integer, 0) as bclicks'),
                DB::raw('COALESCE(binom.lp_clicks::integer, 0) as lp_clicks'),
                DB::raw('COALESCE(binom.lp_views::integer, 0) as lp_views'),
                DB::raw('COALESCE(binom.bleads::integer, 0) as bleads'),
                DB::raw('COALESCE(deposits.cnt::integer, 0) as deposits'),
                DB::raw('COALESCE(deposits.cnt_az::integer, 0) as deposits_az'),
                DB::raw('COALESCE(deposits.cnt::integer, 0) + COALESCE(deposits.cnt_az::integer, 0) as deposits_total'),
                DB::raw('COALESCE(leads.cnt::integer, 0) as board_leads'),
            ])
            ->when($this->shouldAddBenefit(), function (Builder $builder) {
                return $builder->addSelect([
                    DB::raw('COALESCE(deposits.benefit::decimal, 0) as deposits_benefit'),
                    DB::raw('COALESCE(leads.benefit::decimal, 0) as leads_benefit'),
                ])
                    ->groupBy('deposits.benefit', 'leads.benefit');
            })
            ->join('manual_accounts', 'manual_insights.account_id', 'manual_accounts.account_id')
            ->join('manual_campaigns', 'manual_insights.campaign_id', 'manual_campaigns.id')
            ->when($this->groupByUser, function (Builder $builder) {
                return $builder->addSelect('users.name as buyer')
                    ->leftJoin('users', 'manual_accounts.user_id', 'users.id');
            })
            // grouping
            ->when($this->level === self::ACCOUNT, function (Builder $builder) {
                return $builder->addSelect('manual_accounts.name as name')
                    ->leftJoinSub(
                        $this->traffic(),
                        'binom',
                        function (JoinClause $clause) {
                            $clause->on('manual_accounts.name', '=', 'binom.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'binom.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->deposits(),
                        'deposits',
                        function (JoinClause $clause) {
                            $clause->on('manual_accounts.name', '=', 'deposits.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'deposits.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->leads(),
                        'leads',
                        function (JoinClause $clause) {
                            return $clause->on('manual_accounts.name', 'leads.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', 'leads.buyer'));
                        }
                    )
                    ->groupBy('manual_accounts.name');
            })
            ->when($this->level === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect('manual_campaigns.utm_key as name')
                    ->leftJoinSub(
                        $this->traffic(),
                        'binom',
                        function (JoinClause $clause) {
                            $clause->on('manual_campaigns.utm_key', '=', 'binom.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'binom.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->deposits(),
                        'deposits',
                        function (JoinClause $clause) {
                            $clause->on('manual_campaigns.utm_key', '=', 'deposits.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'deposits.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->leads(),
                        'leads',
                        function (JoinClause $clause) {
                            return $clause->on('manual_campaigns.utm_key', 'leads.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', 'leads.buyer'));
                        }
                    )
                    ->groupBy('manual_campaigns.utm_key');
            })
            ->when($this->level === self::CREO, function (Builder $builder) {
                return $builder->addSelect('manual_campaigns.creo as name')
                    ->leftJoinSub(
                        $this->traffic(),
                        'binom',
                        function (JoinClause $clause) {
                            $clause->on('manual_campaigns.creo', '=', 'binom.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'binom.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->deposits(),
                        'deposits',
                        function (JoinClause $clause) {
                            $clause->on('manual_campaigns.creo', '=', 'deposits.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', '=', 'deposits.buyer'));
                        }
                    )
                    ->leftJoinSub(
                        $this->leads(),
                        'leads',
                        function (JoinClause $clause) {
                            return $clause->on('manual_campaigns.creo', 'leads.name')
                                ->when($this->groupByUser, fn ($q) => $q->on('users.name', 'leads.buyer'));
                        }
                    )
                    ->groupBy('manual_campaigns.creo');
            })
            // filtering
            ->whereBetween('manual_insights.date', [$this->since, $this->until])
            ->when($this->users, function ($query) {
                return $query->whereHas('account', fn ($q) => $q->whereIn('user_id', $this->users));
            })
            ->when($this->offers, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->whereHas('bundle', fn ($q1) => $q1->whereIn('offer_id', $this->offers));
                });
            })
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'manual_accounts.user_id')
                        ->whereIn('team_id', $this->teams);
                });
            })
            ->when($this->accounts, fn ($query) => $query->whereIn('manual_insights.account_id', $this->accounts))
            ->when($this->utmCampaigns, function ($query) {
                return $query->whereHas('campaign', fn ($q) => $q->whereIn('utm_key', $this->utmCampaigns));
            })
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_accounts.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->branchUsers, fn ($query) => $query->whereIn('manual_accounts.user_id', $this->branchUsers))
            ->when($this->verticalOffers, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->whereHas('bundle', fn ($q1) => $q1->whereIn('offer_id', $this->verticalOffers));
                });
            })
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('manual_campaigns.utm_key', 'like', "%-{$this->part}%")
                        ->orWhere('manual_campaigns.utm_key', '=', $this->part);
                });
            })
            ->groupBy('bclicks', 'lp_clicks', 'lp_views', 'bleads', 'deposits.cnt', 'deposits.cnt_az', 'leads.cnt')
            ->when($this->groupByUser, function (Builder $builder) {
                return $builder->groupBy('users.name');
            })
            ->get();
    }

    /**
     * Gets binom stats
     *
     * @return Statistic|Builder
     */
    protected function traffic()
    {
        return Statistic::allowedOffers()
            ->select([
                DB::raw('sum(clicks) as bclicks'),
                DB::raw('sum(lp_clicks) as lp_clicks'),
                DB::raw('sum(lp_views) as lp_views'),
                DB::raw('sum(leads) as bleads'),
                DB::raw('sum(cost::decimal) as bcost'),
            ])
            ->join('manual_accounts', 'binom_statistics.account_id', 'manual_accounts.account_id')
            ->when($this->level === self::ACCOUNT, function (Builder $builder) {
                return $builder->addSelect('manual_accounts.name as name')
                    ->groupBy('manual_accounts.name');
            })
            ->when($this->level === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect('binom_statistics.utm_campaign as name')
                    ->groupBy('binom_statistics.utm_campaign');
            })
            ->when($this->level === self::CREO, function (Builder $builder) {
                return $builder->addSelect('binom_statistics.utm_content as name')
                    ->groupBy('binom_statistics.utm_content');
            })
            ->when($this->groupByUser, function (Builder $builder) {
                return $builder->addSelect('users.name as buyer')
                    ->leftJoin('users', 'binom_statistics.user_id', 'users.id')
                    ->groupBy('users.name');
            })
            ->whereBetween('date', [$this->since, $this->until])
            ->when($this->users, fn ($q) => $q->whereIn('binom_statistics.user_id', $this->users))
            ->forOffers($this->offers)
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_accounts.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'binom_statistics.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->accounts, fn ($query) => $query->whereIn('binom_statistics.account_id', $this->accounts))
            ->when($this->utmCampaigns, fn ($query) => $query->whereIn('binom_statistics.utm_campaign', $this->utmCampaigns))
            ->when($this->branchUsers, fn ($query) => $query->whereIn('binom_statistics.user_id', $this->branchUsers))
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('binom_statistics.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('binom_statistics.utm_campaign', '=', $this->part);
                });
            })
            ->forOffers($this->verticalOffers);
    }

    /**
     * Gets deposits
     *
     * @return Deposit|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function deposits()
    {
        return Deposit::allowedOffers()
            ->select([
                DB::raw('count(CASE WHEN lead_destinations.driver != \'hugeoffers\' or lead_destinations.driver IS NULL THEN 1 END) as cnt'),
                DB::raw('count(CASE WHEN lead_destinations.driver = \'hugeoffers\' THEN 1 END) as cnt_az'),
            ])
            ->when($this->shouldAddBenefit(), function (Builder $builder) {
                return $builder->addSelect([
                    DB::raw('sum(deposits.benefit) as benefit'),
                ]);
            })
            ->visible()
            ->leftJoin('lead_order_assignments', function (JoinClause $joinClause) {
                return $joinClause->on('deposits.lead_id', 'lead_order_assignments.lead_id')
                    ->on('deposits.lead_return_date', DB::raw('lead_order_assignments.created_at::date'));
            })
            ->leftJoin('lead_destinations', 'lead_order_assignments.destination_id', 'lead_destinations.id')
            ->leftJoin('manual_accounts', 'deposits.account_id', 'manual_accounts.account_id')
            ->join('leads', 'deposits.lead_id', 'leads.id')
            // dont count deps from resell for buyers
            ->when(auth()->user()->isBuyer(), fn ($query) => $query->whereNotExists(function ($q) {
                return $q->select(DB::raw(1))
                    ->from('lead_resell_batch')
                    ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id');
            }))
            ->when($this->level === self::ACCOUNT, function (Builder $builder) {
                return $builder->addSelect('manual_accounts.name as name')
                    ->groupBy('manual_accounts.name');
            })
            ->when($this->level === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect('leads.utm_campaign as name')
                    ->groupBy('leads.utm_campaign');
            })
            ->when($this->level === self::CREO, function (Builder $builder) {
                return $builder->addSelect('leads.utm_content as name')
                    ->groupBy('leads.utm_content');
            })
            ->when($this->groupByUser, function (Builder $builder) {
                return $builder->addSelect('users.name as buyer')
                    ->leftJoin('users', 'deposits.user_id', 'users.id')
                    ->groupBy('users.name');
            })
            ->whereBetween('lead_return_date', [$this->since, $this->until])
            ->when($this->users, fn ($query) => $query->whereIn('deposits.user_id', $this->users))
            ->when($this->offers, fn ($query) => $query->whereIn('deposits.offer_id', $this->offers))
            ->whereDoesntHave('lead', fn ($query) => $query->whereNotNull('affiliate_id'))
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'deposits.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->accounts, fn ($query) => $query->whereIn('deposits.account_id', $this->accounts))
            ->when($this->utmCampaigns, function ($query) {
                return $query->whereHas('lead', fn ($q) => $q->whereIn('utm_campaign', $this->utmCampaigns));
            })
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'manual_accounts.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->branchUsers, fn ($query) => $query->whereIn('deposits.user_id', $this->branchUsers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('deposits.offer_id', $this->verticalOffers))
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            });
    }

    /**
     * @return Lead|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function leads()
    {
        return Lead::visible()
            ->own()
            ->when(
                $this->shouldAddBenefit(),
                function (Builder $builder) {
                    return $builder->select([
                        DB::raw('count(distinct leads.id) as cnt'),
                        DB::raw('sum(lead_order_assignments.benefit) as benefit'),
                    ])
                        ->leftJoin('lead_order_assignments', function (JoinClause $joinClause) {
                            return $joinClause->on('leads.id', 'lead_order_assignments.lead_id')
                                ->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until])
                                // dont count resell for buyers
                                ->when(auth()->user()->isBuyer(), fn ($query) => $query->whereNotExists(function ($q) {
                                    return $q->select(DB::raw(1))
                                        ->from('lead_resell_batch')
                                        ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id');
                                }));
                        });
                },
                function (Builder $builder) {
                    return $builder->select([
                        DB::raw('count(leads.*) as cnt'),
                    ]);
                }
            )
            ->when($this->level === self::ACCOUNT, function (Builder $builder) {
                return $builder->addSelect('manual_accounts.name as name')
                    ->leftJoin('manual_accounts', 'leads.account_id', 'manual_accounts.account_id')
                    ->groupBy('manual_accounts.name');
            })
            ->when($this->level === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect('leads.utm_campaign as name')
                    ->groupBy('leads.utm_campaign');
            })
            ->when($this->level === self::CREO, function (Builder $builder) {
                return $builder->addSelect('leads.utm_content as name')
                    ->groupBy('leads.utm_content');
            })
            ->when($this->groupByUser, function (Builder $builder) {
                return $builder->addSelect('users.name as buyer')
                    ->leftJoin('users', 'leads.user_id', 'users.id')
                    ->groupBy('users.name');
            })
            ->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until])
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->offers, fn ($query) => $query->whereIn('leads.offer_id', $this->offers))
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->accounts, fn ($query) => $query->whereIn('leads.account_id', $this->accounts))
            ->when($this->utmCampaigns, fn ($query) => $query->whereIn('leads.utm_campaign', $this->utmCampaigns))
            ->when($this->groups, function (Builder $query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('manual_account_manual_group')
                        ->whereColumn('manual_account_manual_group.account_id', '=', 'leads.account_id')
                        ->whereIn('manual_account_manual_group.group_id', $this->groups);
                });
            })
            ->when($this->branchUsers, fn ($query) => $query->whereIn('leads.user_id', $this->branchUsers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('leads.offer_id', $this->verticalOffers))
            ->when($this->part, function (Builder $builder) {
                return $builder->where(function ($query) {
                    return $query->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            });
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
     * @param string $level
     *
     * @return $this
     */
    public function forLevel(string $level)
    {
        if (auth()->user()->isDesigner()) {
            $this->level = self::CREO;
        } else {
            $this->level = in_array($level, self::LEVELS) ? $level : self::CAMPAIGN;
        }

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
        if ($users !== null) {
            $this->users = User::visible()->pluck('id')->intersect($users)->values()->toArray();

            return $this;
        }

        $this->users = User::visible()->pluck('id')->toArray();

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
        if (auth()->user()->isDesigner()) {
            $this->teams = auth()->user()->teams()
                ->when($teams, fn ($query) => $query->whereIn('teams.id', $teams))
                ->pluck('teams.id')
                ->push(0)
                ->toArray();
        } else {
            $this->teams = $teams;
        }

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
        if ($accounts !== null) {
            $this->accounts = ManualAccount::visible()
                ->whereHas('insights', fn (Builder $query) => $query->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()]))
                ->pluck('account_id')
                ->intersect($accounts)
                ->values()
                ->toArray();

            return $this;
        }

        $this->accounts = ManualAccount::visible()
            ->whereHas('insights', fn (Builder $query) => $query->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()]))
            ->pluck('account_id')->toArray();

        return $this;
    }
    /**
     * Filter by offers
     *
     * @param null|array $utmCampaigns
     *
     * @return $this
     */
    public function forUtmCampaigns($utmCampaigns)
    {
        $this->utmCampaigns = $utmCampaigns;

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

    protected function loadCampaignOffers()
    {
        if ($this->level === self::CAMPAIGN) {
            $this->campaignOffers = ManualCampaign::query()
                ->with(['offer', 'bundle', 'bundle.offer'])
                ->when($this->offers, function (Builder $builder) {
                    return $builder->whereHas('bundle', fn ($query) => $query->whereIn('offer_id', $this->offers));
                })
                ->get()
                ->groupBy('utm_key');

            return;
        }

        $this->campaignOffers = collect();
    }

    /**
     * @param bool $byUser
     *
     * @return $this
     */
    public function groupByUser(bool $byUser)
    {
        $this->groupByUser = $byUser;

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

    protected function shouldAddBenefit()
    {
        if (
            auth()->user()->isBuyer() && !in_array(auth()->user()->name, ['SHMN', 'BRIT', 'MALOY'])
            && auth()->user()->branch_id !== 25
        ) {
            return false;
        }

        return auth()->user()->displayRevenue()
            && in_array(auth()->user()->branch_id, [16, 25]);
    }
}
