<?php

namespace App\Reports\OfficePerformance;

use App\Affiliate;
use App\Deposit;
use App\LeadOrderAssignment;
use App\Offer;
use App\Office;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Report implements Responsable, Arrayable
{
    public const OFFER_ORIGINAL  = 'original';
    public const OFFER_DUPLICATE = 'duplicate';
    public const OFFER_CD        = 'cd';

    public const OFFER_TYPES = [
        self::OFFER_ORIGINAL,
        self::OFFER_DUPLICATE,
        self::OFFER_CD,
    ];

    public const TRAFFIC_AFFILIATED     = 'affiliated';
    public const TRAFFIC_NOT_AFFILIATED = 'not_affiliated';

    public const TRAFFIC_IN_APP = 'in-app';
    public const TRAFFIC_PS     = 'ps';
    public const TRAFFIC_T      = 't';
    public const TRAFFIC_VK     = 'vk';
    public const TRAFFIC_YD     = 'yd';

    public const TRAFFIC_SOURCES = [
        self::TRAFFIC_IN_APP,
        self::TRAFFIC_PS,
        self::TRAFFIC_T,
        self::TRAFFIC_VK,
        self::TRAFFIC_YD,
    ];

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
     * Offices used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offices;

    /**
     * Offers used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

    /**
     * Branches used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $branches;

    /**
     * @var string|null
     */
    protected ?string $offerType = null;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $officeGroups;

    /**
     * @var string
     */
    protected ?string $trafficType = null;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $affiliates;

    /**
     * @var string|null
     */
    protected ?string $trafficSource = null;

    /**
     * @var array|null
     */
    protected $utmSources = null;

    /**
     * @var Deposit[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $results;

    /**
     * @var boolean
     */
    protected $groupByUtmSource;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'            => $request->get('since'),
            'until'            => $request->get('until'),
            'offices'          => $request->get('offices'),
            'offers'           => $request->get('offers'),
            'branches'         => $request->get('branches'),
            'offer_type'       => $request->get('offer_type'),
            'office_groups'    => $request->get('office_groups'),
            'traffic_type'     => $request->get('traffic_type'),
            'affiliates'       => $request->get('affiliates'),
            'traffic_source'   => $request->get('traffic_source'),
            'groupByUtmSource' => $request->boolean('groupByUtmSource', false),
            'utmSources'       => $request->get('utmSources'),
        ]);
    }

    /**
     * Constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forOffers($settings['offers'] ?? null)
            ->forOffices($settings['offices'] ?? null)
            ->forBranches($settings['branches'] ?? null)
            ->forOfferType($settings['offer_type'] ?? null)
            ->forOfficeGroups($settings['office_groups'] ?? null)
            ->forTrafficType($settings['traffic_type'] ?? null)
            ->forAffiliates($settings['affiliates'] ?? null)
            ->forTrafficSource($settings['traffic_source'] ?? null)
            ->groupByUtmSource($settings['groupByUtmSource'] ?? false)
            ->forUtmSources($settings['utmSources'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return Report
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
     * @return Report
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
     * Filter results for specific offers
     *
     * @param array|string $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = is_null($offers) ? Offer::allowed()->pluck('id')->values() : Offer::whereIn('id', Arr::wrap($offers))->pluck('id')->values();

        return $this;
    }

    /**
     * Filter results for specific offices
     *
     * @param array|string $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = is_null($offices) ? Office::visible()->pluck('id')->values() : Office::whereIn('id', Arr::wrap($offices))->pluck('id')->values();

        $this->offices->add(null);

        return $this;
    }

    /**
     * Filter results for specific branches
     *
     * @param array|string $branches
     *
     * @return Report
     */
    public function forBranches($branches = null)
    {
        if (!empty($branches)) {
            $this->branches = Arr::wrap($branches);
        }

        return $this;
    }

    /**
     * @param string|null $offerType
     *
     * @return Report
     */
    public function forOfferType($offerType = null)
    {
        if (!empty($offerType) && in_array($offerType, self::OFFER_TYPES)) {
            $this->offerType = $offerType;
        }

        return $this;
    }

    /**
     * @param array|string $groups
     *
     * @return Report
     */
    public function forOfficeGroups($groups = null)
    {
        if (!empty($groups)) {
            $this->officeGroups = $groups;
        }

        return $this;
    }

    /**
     * @param array|string $affiliates
     *
     * @return Report
     */
    public function forAffiliates($affiliates = null)
    {
        if (!empty($affiliates)) {
            $this->affiliates = Affiliate::whereIn('name', Arr::wrap($affiliates))->pluck('id')->push(0);
        }

        return $this;
    }

    /**
     * @param $trafficType
     *
     * @return $this
     */
    public function forTrafficType($trafficType = null)
    {
        if (!empty($trafficType)) {
            $this->trafficType = $trafficType;
        }

        return $this;
    }

    /**
     * @param string|null $trafficSource
     *
     * @return Report
     */
    public function forTrafficSource($trafficSource = null)
    {
        if (!empty($trafficSource) && in_array($trafficSource, self::TRAFFIC_SOURCES)) {
            $this->trafficSource = $trafficSource;
        }

        return $this;
    }

    /**
     * @param bool $groupByUtmSource
     *
     * @return Report
     */
    public function groupByUtmSource(bool $groupByUtmSource = true)
    {
        $this->groupByUtmSource = $groupByUtmSource;

        return $this;
    }

    /**
     * @param null $utmSources
     *
     * @return Report
     */
    public function forUtmSources($utmSources = null)
    {
        $this->utmSources = $utmSources;

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
        $this->results();

        return [
            'headers'  => $this->headers(),
            'rows'     => $this->rows(),
            'summary'  => $this->summary(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * Gets offices
     */
    protected function results()
    {
        $this->results = LeadOrderAssignment::visible()
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', '=', 'leads_orders.id')
            ->join('offices', 'leads_orders.office_id', '=', 'offices.id')
            ->join('leads', 'leads.id', '=', 'lead_order_assignments.lead_id')
            ->join('offers', 'leads.offer_id', '=', 'offers.id')
            ->leftJoin('deposits', 'lead_order_assignments.lead_id', '=', 'deposits.lead_id')
            ->select([
                DB::raw('offices.name as office'),
                DB::raw('count(distinct lead_order_assignments.id) as leads'),
                DB::raw('count(distinct deposits.id) as deposits'),
                DB::raw('COALESCE(late_deposits.cnt, 0) as late_deposits'),
                DB::raw('(CASE WHEN count(distinct lead_order_assignments.id) > 0 THEN (count(distinct deposits.id) * 100 / count(distinct lead_order_assignments.id))::decimal ELSE 0 END) as conversion'),

            ])
            ->when(
                $this->trafficSource === self::TRAFFIC_IN_APP,
                fn ($builder) => $builder->addSelect(DB::raw("regexp_replace(offers.name, '_IN|_APP', '') as offerok")),
            )
            ->when(
                $this->trafficSource === self::TRAFFIC_PS,
                fn ($builder) => $builder->addSelect(DB::raw("regexp_replace(offers.name, '_PS|_AD|_TZ', '') as offerok"))
            )
            ->unless(
                in_array($this->trafficSource, [self::TRAFFIC_IN_APP, self::TRAFFIC_PS]),
                fn ($builder) => $builder->addSelect('offers.name as offerok')
            )
            ->when($this->groupByUtmSource, fn ($query) => $query->addSelect('leads.utm_source as utm_source'))
            ->leftJoinSub($this->lateDeposits(), 'late_deposits', function (JoinClause $joinClause) {
                return $joinClause->on('offices.name', 'late_deposits.office')
                    ->on(
                        $this->trafficSource === self::TRAFFIC_IN_APP
                            ? DB::raw("regexp_replace(offers.name, '_IN|_APP', '')")
                            : (
                                $this->trafficSource === self::TRAFFIC_PS
                                ? DB::raw("regexp_replace(offers.name, '_PS|_AD|_TZ', '')")
                                : 'offers.name'
                            ),
                        'late_deposits.offer'
                    )
                    ->when($this->groupByUtmSource, fn ($q) => $q->on('leads.utm_source', 'late_deposits.utm_source'));
            })
            ->when($this->offerType === self::OFFER_ORIGINAL, function (\Illuminate\Database\Eloquent\Builder $builder) {
                return $builder->where('offers.name', 'not like', '%\_D');
            })
            ->when($this->offerType === self::OFFER_DUPLICATE, function (\Illuminate\Database\Eloquent\Builder $builder) {
                return $builder->where('offers.name', 'like', '%\_D');
            })
            ->when(
                $this->offerType === self::OFFER_CD,
                fn ($builder) => $builder->where('offers.name', 'like', '%\_CD')
            )
            ->when($this->branches, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->whereIn('offices.id', $this->offices)
            ->when($this->officeGroups, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereNotNull('leads.affiliate_id');
            })
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereNull('leads.affiliate_id');
            })
            ->whereIn('leads.offer_id', $this->offers)
            ->when($this->affiliates, fn ($query) => $query->whereIn('leads.affiliate_id', $this->affiliates))
            ->when($this->trafficSource === self::TRAFFIC_IN_APP, function ($builder) {
                return $builder->where(function ($query) {
                    return $query->where('offers.name', 'like', '%\_IN%')
                        ->orWhere('offers.name', 'like', '%\_APP%');
                });
            })
            ->when($this->trafficSource === self::TRAFFIC_PS, function ($builder) {
                return $builder->where(function ($query) {
                    return $query->where('offers.name', 'like', '%\_PS%')
                        ->orWhere('offers.name', 'like', '%\_AD%')
                        ->orWhere('offers.name', 'like', '%\_TZ%');
                });
            })
            ->when($this->trafficSource === self::TRAFFIC_T, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_T%');
            })
            ->when($this->trafficSource === self::TRAFFIC_VK, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_VK%');
            })
            ->when($this->trafficSource === self::TRAFFIC_YD, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_YD%');
            })
            ->whereBetween('lead_order_assignments.created_at', [
                $this->since->startOfDay()->toDateTimeString(),
                $this->until->endOfDay()->toDateTimeString()
            ])
            ->when($this->utmSources, fn ($query) => $query->whereIn('leads.utm_source', $this->utmSources))
            ->groupBy('offices.name', 'offerok', 'late_deposits.cnt')
            ->when($this->groupByUtmSource, fn ($query) => $query->groupBy('leads.utm_source'))
            ->orderBy('conversion')
            ->get();
    }

    public function lateDeposits()
    {
        return Deposit::visible()
            ->select([
                DB::raw('count(distinct deposits.id) as cnt'),
                DB::raw('offices.name as office'),
            ])
            ->when(
                $this->trafficSource === self::TRAFFIC_IN_APP,
                fn ($builder) => $builder->addSelect(DB::raw("regexp_replace(offers.name, '_IN|_APP', '') as offer")),
            )
            ->when(
                $this->trafficSource === self::TRAFFIC_PS,
                fn ($builder) => $builder->addSelect(DB::raw("regexp_replace(offers.name, '_PS|_AD|_TZ', '') as offer"))
            )
            ->unless(
                in_array($this->trafficSource, [self::TRAFFIC_IN_APP, self::TRAFFIC_PS]),
                fn ($builder) => $builder->addSelect('offers.name as offer')
            )
            ->join('offices', 'deposits.office_id', 'offices.id')
            ->join('offers', 'deposits.offer_id', 'offers.id')
            ->leftJoin('leads', 'deposits.lead_id', 'leads.id')
            ->when($this->groupByUtmSource, function ($query) {
                return $query
                    ->selectRaw(
                        "CASE
                            WHEN leads.utm_source is null THEN ''
                            WHEN leads.utm_source is not null THEN leads.utm_source
                        END as utm_source"
                    )->groupBy('utm_source');
            })
            ->when($this->offerType === self::OFFER_ORIGINAL, function (\Illuminate\Database\Eloquent\Builder $builder) {
                return $builder->where('offers.name', 'not like', '%\_D');
            })
            ->when($this->offerType === self::OFFER_DUPLICATE, function (\Illuminate\Database\Eloquent\Builder $builder) {
                return $builder->where('offers.name', 'like', '%\_D');
            })
            ->when(
                $this->offerType === self::OFFER_CD,
                fn ($builder) => $builder->where('offers.name', 'like', '%\_CD')
            )
            ->whereIn('deposits.office_id', $this->offices)
            ->when($this->officeGroups, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, fn ($query) => $query->whereNotNull('leads.affiliate_id'))
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, fn ($query) => $query->whereNull('leads.affiliate_id'))
            ->whereIn('deposits.offer_id', $this->offers)
            ->whereBetween('deposits.date', [$this->since, $this->until])
            ->where('deposits.lead_return_date', '<', $this->since)
            ->when($this->branches, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'deposits.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->when($this->affiliates, fn ($query) => $query->whereIn('leads.affiliate_id', $this->affiliates))
            ->when($this->trafficSource === self::TRAFFIC_IN_APP, function ($builder) {
                return $builder->where(function ($query) {
                    return $query->where('offers.name', 'like', '%\_IN%')
                        ->orWhere('offers.name', 'like', '%\_APP%');
                });
            })
            ->when($this->trafficSource === self::TRAFFIC_PS, function ($builder) {
                return $builder->where(function ($query) {
                    return $query->where('offers.name', 'like', '%\_PS%')
                        ->orWhere('offers.name', 'like', '%\_AD%')
                        ->orWhere('offers.name', 'like', '%\_TZ%');
                });
            })
            ->when($this->trafficSource === self::TRAFFIC_T, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_T%');
            })
            ->when($this->trafficSource === self::TRAFFIC_VK, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_VK%');
            })
            ->when($this->trafficSource === self::TRAFFIC_YD, function ($builder) {
                return $builder->where('offers.name', 'like', '%\_YD%');
            })
            ->when($this->utmSources, fn ($query) => $query->whereIn('leads.utm_source', $this->utmSources))
            ->groupBy(['offices.name', 'offer']);
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function rows()
    {
        return $this->results->map(function ($row) {
            $result = [
                Fields::OFFICE           => $row->office,
                Fields::OFFER            => $row->offerok,
                Fields::UTM_SOURCE       => $row->utm_source,
                Fields::LEADS            => $row->leads ?: 0,
                Fields::DEPOSITS         => $row->deposits ?: 0,
                Fields::CONVERSION       => percentage($row->deposits, $row->leads),
                Fields::LATE_DEPOSITS    => $row->late_deposits ?: 0,
                Fields::LATE_CONVERSION  => percentage($row->late_deposits, $row->leads),
                Fields::TOTAL_DEPOSITS   => $row->late_deposits + $row->deposits,
                Fields::TOTAL_CONVERSION => percentage($row->late_deposits + $row->deposits, $row->leads),
            ];

            if (!$this->groupByUtmSource) {
                unset($result[Fields::UTM_SOURCE]);
            }

            return $result;
        });
    }

    /**
     * @return array
     */
    protected function summary()
    {
        $result = [
            Fields::OFFICE           => 'Итого',
            Fields::OFFER            => '',
            Fields::UTM_SOURCE       => '',
            Fields::LEADS            => $this->results->sum('leads') ?: 0,
            Fields::DEPOSITS         => $this->results->sum('deposits') ?: 0,
            Fields::CONVERSION       => percentage($this->results->sum('deposits'), $this->results->sum('leads')),
            Fields::LATE_DEPOSITS    => $this->results->sum('late_deposits'),
            Fields::LATE_CONVERSION  => percentage($this->results->sum('late_deposits'), $this->results->sum('leads')),
            Fields::TOTAL_DEPOSITS   => $this->results->sum('late_deposits') + $this->results->sum('deposits'),
            Fields::TOTAL_CONVERSION => percentage($this->results->sum('late_deposits') + $this->results->sum('deposits'), $this->results->sum('leads')),
        ];

        if (!$this->groupByUtmSource) {
            unset($result[Fields::UTM_SOURCE]);
        }

        return $result;
    }

    protected function headers()
    {
        $result = Headers::ALL;

        $except = [];

        if (!$this->groupByUtmSource) {
            $except[] = Headers::UTM_SOURCE;
        }

        $result = array_filter($result, fn ($header) => !in_array($header, $except));

        return $result;
    }
}
