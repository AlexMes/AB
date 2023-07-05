<?php

namespace App\Deluge\Reports\BuyerStats;

use App\Binom\Statistic;
use App\Deposit;
use App\Lead;
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
    public const PART_FB    = 'fb';
    public const PART_TT    = 'tt';
    public const PART_VK    = 'vk';
    public const PART_UT    = 'ut';
    public const PART_KD    = 'kd';
    public const PARTS      = [
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
            'users'         => $request->get('users'),
            'teams'         => $request->get('teams'),
            'accounts'      => $request->get('accounts'),
            'utm_campaigns' => $request->get('utm_campaigns'),
            'branches'      => $request->get('branches'),
            'vertical'      => $request->get('vertical'),
            'part'          => $request->get('part'),
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
            ->forAccounts($settings['accounts'])
            ->forUtmCampaigns($settings['utm_campaigns'])
            ->forBranches($settings['branches'])
            ->forVertical($settings['vertical'])
            ->forPart($settings['part']);

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
            'buyer'       => 'buyer',
            'rk'          => 'РК',
            'impressions' => 'impressions',
            'clicks'      => 'clicks',
            'cpm'         => 'cpm',
            'cpc'         => 'cpc',
            'ctr'         => 'ctr',

            'bclicks'     => 'b.clicks',
            'lp_views'    => 'LP Views',
            'lp_clicks'   => 'LP Clicks',
            'lp_ctr'      => 'LP CTR',
            'offer_cr'    => 'Offer CR',
            'cr'          => 'CR',
            'board_leads' => 'board leads',
            'leads'       => 'leads',
            'bleads'      => 'b.leads',

            'ftd'          => 'ftd',
            'ftd_percent'  => 'ftd%',
            'ftd_cost'     => 'ftd cost',
            'bftd_percent' => 'b.ftd%',
            'rev'          => 'rev',
            'fb_cpl'       => 'fb.cpl',
            'bcpl'         => 'b.cpl',
            'cost'         => 'cost',
            'profit'       => 'profit',
            'roi'          => 'roi',
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
            $revenue = $item->deposits * 400 + $item->deposits_az * 300;

            return [
                'buyer'       => $item->buyer ?? '',
                'rk'          => $item->accounts,
                'impressions' => $item->impressions,
                'clicks'      => $item->clicks,
                'cpm'         => round($item->impressions > 0 ? $item->spend / ($item->impressions / 1000) : 0, 2),
                'cpc'         => round($item->clicks > 0 ? $item->spend / $item->clicks : 0, 2),
                'ctr'         => percentage($item->clicks, $item->impressions),

                'bclicks'     => $item->bclicks,
                'lp_views'    => $item->lp_views,
                'lp_clicks'   => $item->lp_clicks,
                'lp_ctr'      => percentage($item->lp_clicks, $item->lp_views),
                'offer_cr'    => percentage($item->bleads, $item->lp_clicks),
                'cr'          => percentage($item->bleads, $item->bclicks),
                'board_leads' => $item->board_leads,
                'leads'       => $item->leads_cnt,
                'bleads'      => $item->bleads,

                'ftd'          => $item->deposits_total,
                'ftd_percent'  => percentage($item->deposits_total, $item->leads_cnt),
                'ftd_cost'     => round($item->deposits_total > 0 ? $item->spend / $item->deposits_total : 0, 2),
                'bftd_percent' => percentage($item->deposits_total, $item->bleads),
                'rev'          => $revenue,
                'fb_cpl'       => round($item->leads_cnt > 0 ? $item->spend / $item->leads_cnt : 0, 2),
                'bcpl'         => round($item->bleads > 0 ? $item->spend / $item->bleads : 0, 2),
                'cost'         => $item->spend,
                'profit'       => $revenue - $item->spend,
                'roi'          => percentage($revenue - $item->spend, $item->spend),
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
        $revenue = $data->sum('deposits') * 400 + $data->sum('deposits_az') * 300;

        return [
            'buyer'       => 'Итого',
            'rk'          => $data->sum('accounts'),
            'impressions' => $data->sum('impressions'),
            'clicks'      => $data->sum('clicks'),
            'cpm'         => round(
                $data->sum('impressions') > 0
                    ? $data->sum('spend') / ($data->sum('impressions') / 1000)
                    : 0,
                2
            ),
            'cpc'         => round(
                $data->sum('clicks') > 0
                    ? $data->sum('spend') / $data->sum('clicks')
                    : 0,
                2
            ),
            'ctr'         => percentage($data->sum('clicks'), $data->sum('impressions')),

            'bclicks'     => $data->sum('bclicks'),
            'lp_views'    => $data->sum('lp_views'),
            'lp_clicks'   => $data->sum('lp_clicks'),
            'lp_ctr'      => percentage($data->sum('lp_clicks'), $data->sum('lp_views')),
            'offer_cr'    => percentage($data->sum('bleads'), $data->sum('lp_clicks')),
            'cr'          => percentage($data->sum('bleads'), $data->sum('bclicks')),
            'board_leads' => $data->sum('board_leads'),
            'leads'       => $data->sum('leads_cnt'),
            'bleads'      => $data->sum('bleads'),

            'ftd'           => $data->sum('deposits_total'),
            'ftd_percent'   => percentage($data->sum('deposits_total'), $data->sum('leads_cnt')),
            'ftd_cost'      => round($data->sum('deposits_total') > 0 ? $data->sum('spend') / $data->sum('deposits_total') : 0, 2),
            'bftd_percent'  => percentage($data->sum('deposits_total'), $data->sum('bleads')),
            'rev'           => $revenue,
            'fb_cpl'        => round(
                $data->sum('leads_cnt') > 0 ?
                    $data->sum('spend') / $data->sum('leads_cnt')
                    : 0,
                2
            ),
            'bcpl'        => round($data->sum('bleads') > 0 ? $data->sum('spend') / $data->sum('bleads') : 0, 2),
            'cost'        => $data->sum('spend'),
            'profit'      => $revenue - $data->sum('spend'),
            'roi'         => percentage($revenue - $data->sum('spend'), $data->sum('spend')),
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
                DB::raw('count(distinct manual_accounts.account_id) as accounts'),
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(clicks) as clicks'),
                DB::raw('sum(spend) as spend'),
                DB::raw('sum(leads_cnt) as leads_cnt'),
                DB::raw('COALESCE(binom.bclicks::integer, 0) as bclicks'),
                DB::raw('COALESCE(binom.lp_clicks::integer, 0) as lp_clicks'),
                DB::raw('COALESCE(binom.lp_views::integer, 0) as lp_views'),
                DB::raw('COALESCE(binom.bleads::integer, 0) as bleads'),
                DB::raw('COALESCE(deposits.cnt::integer, 0) as deposits'),
                DB::raw('COALESCE(deposits.cnt_az::integer, 0) as deposits_az'),
                DB::raw('COALESCE(deposits.cnt::integer, 0) + COALESCE(deposits.cnt_az::integer, 0) as deposits_total'),
                DB::raw('COALESCE(leads.cnt::integer, 0) as board_leads'),
            ])
            ->join('manual_accounts', 'manual_insights.account_id', 'manual_accounts.account_id')
            ->leftJoin('users', 'manual_accounts.user_id', 'users.id')
            ->leftJoinSub(
                $this->traffic(),
                'binom',
                'users.name',
                'binom.buyer',
            )
            ->leftJoinSub(
                $this->deposits(),
                'deposits',
                'users.name',
                'deposits.buyer',
            )
            ->leftJoinSub(
                $this->leads(),
                'leads',
                'users.name',
                'leads.buyer',
            )
            // filtering
            ->whereBetween('manual_insights.date', [$this->since, $this->until])
            ->when($this->users, function ($query) {
                return $query->whereHas('account', fn ($q) => $q->whereIn('user_id', $this->users));
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
            ->when($this->branchUsers, fn ($query) => $query->whereIn('manual_accounts.user_id', $this->branchUsers))
            ->when($this->verticalOffers, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->whereHas('bundle', fn ($q1) => $q1->whereIn('offer_id', $this->verticalOffers));
                });
            })
            ->when($this->part, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->where('manual_campaigns.utm_key', 'like', "%-{$this->part}%")
                        ->orWhere('manual_campaigns.utm_key', '=', $this->part);
                });
            })
            ->groupBy('users.name', 'bclicks', 'lp_clicks', 'lp_views', 'bleads', 'deposits.cnt', 'deposits.cnt_az', 'leads.cnt')
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
                DB::raw('users.name as buyer'),
                DB::raw('sum(clicks) as bclicks'),
                DB::raw('sum(lp_clicks) as lp_clicks'),
                DB::raw('sum(lp_views) as lp_views'),
                DB::raw('sum(leads) as bleads'),
            ])
            ->leftJoin('users', 'binom_statistics.user_id', 'users.id')
            ->whereBetween('date', [$this->since, $this->until])
            ->when($this->users, fn ($q) => $q->whereIn('binom_statistics.user_id', $this->users))
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
            ->forOffers($this->verticalOffers)
            ->when($this->part, function ($query) {
                return $query->where(function ($q) {
                    return $q->where('binom_statistics.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('binom_statistics.utm_campaign', '=', $this->part);
                });
            })
            ->groupBy('users.name');
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
                DB::raw('users.name as buyer'),
                DB::raw('count(CASE WHEN lead_destinations.driver != \'hugeoffers\' or lead_destinations.driver IS NULL THEN 1 END) as cnt'),
                DB::raw('count(CASE WHEN lead_destinations.driver = \'hugeoffers\' THEN 1 END) as cnt_az'),
            ])
            ->leftJoin('users', 'deposits.user_id', 'users.id')
            ->leftJoin('lead_order_assignments', function (JoinClause $joinClause) {
                return $joinClause->on('deposits.lead_id', 'lead_order_assignments.lead_id')
                    ->on('deposits.lead_return_date', DB::raw('lead_order_assignments.created_at::date'));
            })
            ->leftJoin('lead_destinations', 'lead_order_assignments.destination_id', 'lead_destinations.id')
            // dont count deps from resell for buyers
            ->when(auth()->user()->isBuyer(), fn ($query) => $query->whereNotExists(function ($q) {
                return $q->select(DB::raw(1))
                    ->from('lead_resell_batch')
                    ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id');
            }))
            ->whereBetween('lead_return_date', [$this->since, $this->until])
            ->when($this->users, fn ($query) => $query->whereIn('deposits.user_id', $this->users))
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
            ->when($this->branchUsers, fn ($query) => $query->whereIn('deposits.user_id', $this->branchUsers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('deposits.offer_id', $this->verticalOffers))
            ->when($this->utmCampaigns, function ($query) {
                return $query->whereHas('lead', fn ($q) => $q->whereIn('utm_campaign', $this->utmCampaigns));
            })
            ->when($this->part, function ($query) {
                return $query->whereHas('lead', function ($q) {
                    return $q->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            })
            ->groupBy('users.name');
    }

    /**
     * @return Lead|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function leads()
    {
        return Lead::visible()
            ->own()
            ->select([
                DB::raw('users.name as buyer'),
                DB::raw('count(leads.*) as cnt'),
            ])
            ->leftJoin('users', 'leads.user_id', 'users.id')
            ->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until])
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->teams, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->accounts, fn ($query) => $query->whereIn('leads.account_id', $this->accounts))
            ->when($this->branchUsers, fn ($query) => $query->whereIn('leads.user_id', $this->branchUsers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('leads.offer_id', $this->verticalOffers))
            ->when($this->utmCampaigns, fn ($query) => $query->whereIn('leads.utm_campaign', $this->utmCampaigns))
            ->when($this->part, function ($query) {
                return $query->where(function ($q) {
                    return $q->where('leads.utm_campaign', 'like', "%-{$this->part}%")
                        ->orWhere('leads.utm_campaign', '=', $this->part);
                });
            })
            ->groupBy('users.name');
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
        $this->teams = $teams;

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
     * Filter by utm campaign
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
