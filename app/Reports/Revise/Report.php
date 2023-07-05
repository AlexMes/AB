<?php

namespace App\Reports\Revise;

use App\CRM\Status;
use App\Deposit;
use App\Lead;
use App\LeadDestination;
use App\LeadOrderAssignment;
use App\LeadPaymentCondition;
use App\Offer;
use App\Office;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\Revise
 */
class Report implements Responsable, Arrayable
{
    public const GROUP_OFFICE                = 'office';
    public const GROUP_BRANCH                = 'branch';
    public const GROUP_USER                  = 'user';
    public const GROUP_OFFER                 = 'offer';
    public const GROUP_USER_OFFICE           = 'user-office';
    public const GROUP_USER_OFFICE_OFFER     = 'user-office-offer';
    public const GROUP_OFFICE_OFFER          = 'office-offer';
    public const GROUP_OFFICE_OFFER_DEST     = 'office-offer-destination';
    public const GROUP_OFFER_GEO             = 'offer-geo';
    public const GROUP_OFFICE_OFFER_GEO      = 'office-offer-geo';
    public const GROUP_USER_GEO              = 'user-geo';
    public const GROUP_USER_GEO_OFFICE       = 'user-geo-office';
    public const GROUP_USER_OFFICE_OFFER_GEO = 'user-office-offer-geo';
    public const GROUP_DESTINATION           = 'destination';
    public const GROUP_OFFICE_OFFER_PRICE    = 'office-offer-model-cost';
    public const GROUP_OFFICE_STATUS         = 'office-status';
    public const GROUP_OFFICE_OFFER_STATUS   = 'office-offer-status';
    public const GROUP_APP                   = 'app';
    public const GROUP_STATUS                = 'status';
    public const GROUP_OFFER_SOURCE          = 'offer-source';
    public const GROUP_USER_DEST             = 'user-destination';
    public const GROUP_USER_OFFER_DEST       = 'user-offer-destination';

    public const GROUP_LIST = [
        self::GROUP_OFFICE,
        self::GROUP_OFFICE_OFFER,
        self::GROUP_BRANCH,
        self::GROUP_USER,
        self::GROUP_OFFER,
        self::GROUP_USER_OFFICE,
        self::GROUP_USER_OFFICE_OFFER,
        self::GROUP_OFFICE_OFFER_DEST,
        self::GROUP_OFFER_GEO,
        self::GROUP_OFFICE_OFFER_GEO,
        self::GROUP_USER_GEO,
        self::GROUP_USER_GEO_OFFICE,
        self::GROUP_USER_OFFICE_OFFER_GEO,
        self::GROUP_DESTINATION,
        self::GROUP_OFFICE_OFFER_PRICE,
        self::GROUP_OFFICE_STATUS,
        self::GROUP_OFFICE_OFFER_STATUS,
        self::GROUP_APP,
        self::GROUP_STATUS,
        self::GROUP_OFFER_SOURCE,
        self::GROUP_USER_DEST,
        self::GROUP_USER_OFFER_DEST,
    ];

    public const TRAFFIC_AFFILIATED     = 'affiliated';
    public const TRAFFIC_NOT_AFFILIATED = 'not_affiliated';

    public const TRAFFIC_IN_APP = 'in-app';
    public const TRAFFIC_PS     = 'ps';
    public const TRAFFIC_T      = 't';
    public const TRAFFIC_VK     = 'vk';

    public const TRAFFIC_SOURCES = [
        self::TRAFFIC_IN_APP,
        self::TRAFFIC_PS,
        self::TRAFFIC_T,
        self::TRAFFIC_VK,
    ];

    public const OFFER_TYPE_LO     = 'lo';
    public const OFFER_TYPE_NOT_LO = 'not_lo';
    public const OFFER_TYPE_CD     = 'cd';
    public const OFFER_TYPE_NOT_CD = 'not_cd';

    public const OFFER_TYPES = [
        self::OFFER_TYPE_LO,
        self::OFFER_TYPE_NOT_LO,
        self::OFFER_TYPE_CD,
        self::OFFER_TYPE_NOT_CD,
    ];

    /**
     * @var string|null
     */
    protected ?string $trafficType = null;

    /**
     * @var string|null
     */
    protected ?string $trafficSource = null;

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
     * Determines how to group statistics
     *
     * @var string|null
     */
    protected $groupBy;

    /**
     * @var array|null
     */
    protected $branches = null;

    /**
     * @var array|null
     */
    protected $teams = null;

    /**
     * @var array|null
     */
    protected $users = null;

    /**
     * @var array|null
     */
    protected $offices = null;

    /**
     * @var |null
     */
    protected $offers = null;

    /**
     * @var string|null
     */
    protected $vertical = null;

    /**
     * @var string|null
     */
    protected $offerType = null;

    /**
     * @var array|null
     */
    protected $destinations = null;

    /**
     * @var array|null
     */
    protected $officeGroups = null;

    /**
     * @var bool
     */
    protected $wnFact = false;

    /**
     * @var string|null
     */
    protected $batches = null;

    /**
     * @var string|null
     */
    protected $modelPayment = null;

    /**
     * @var array|null
     */
    protected $utmSources = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Revise\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'          => $request->get('since'),
            'until'          => $request->get('until'),
            'groupBy'        => $request->get('groupBy'),
            'branches'       => $request->get('branches'),
            'batches'        => $request->get('batches'),
            'teams'          => $request->get('teams'),
            'users'          => $request->get('users'),
            'offices'        => $request->get('offices'),
            'offers'         => $request->get('offers'),
            'vertical'       => $request->get('vertical'),
            'offerType'      => $request->get('offerType'),
            'drivers'        => $request->get('drivers'),
            'officeGroups'   => $request->get('officeGroups'),
            'wnFact'         => $request->boolean('wnFact'),
            'traffic_source' => $request->get('traffic_source'),
            'traffic_type'   => $request->get('traffic_type'),
            'payments'       => $request->get('payments'),
            'utmSources'     => $request->get('utmSources'),
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
            ->groupBy($settings['groupBy'] ?? null)
            ->forBranches($settings['branches'] ?? null)
            ->forBatches($settings['batches'] ?? null)
            ->forTeams($settings['teams'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forOffices($settings['offices'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->forVertical($settings['vertical'] ?? null)
            ->forOfferType($settings['offerType'] ?? null)
            ->forDrivers($settings['drivers'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null)
            ->forTrafficType($settings['traffic_type'] ?? null)
            ->forTrafficSource($settings['traffic_source'] ?? null)
            ->forWnFact($settings['wnFact'] ?? false)
            ->forModelPayment($settings['payments'] ?? null)
            ->forUtmSources($settings['utmSources'] ?? null);
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
            'headers' => $this->headers(),
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Collection
     */
    protected function aggregate()
    {
        return DB::query()
            ->selectRaw("
                COALESCE(ROUND(sum(leads.leads) / count(leads.*)), 0) AS leads,
                COALESCE(ROUND(sum(leads.confirmed) / count(leads.*)), 0) AS confirmed,
                COALESCE(ROUND(sum(leads.nbt) / count(leads.*)), 0) AS wn,
                COALESCE(ROUND(sum(leads.duplicate) / count(leads.*)), 0) AS duplicate,
                COALESCE(ROUND(sum(leads.under) / count(leads.*)), 0) AS under,
                COALESCE(ROUND(sum(leads.invalid) / count(leads.*)), 0) AS invalid,
                COALESCE(ROUND(sum(leads.other) / count(leads.*)), 0) AS other,
                COALESCE(ROUND(sum(leads.no_answer) / count(leads.*)), 0) AS no_answer,
                COALESCE(ROUND(sum(deps.deposits) / count(deps.*)), 0) AS ftd,
                COALESCE(ROUND(sum(deps.intradep) / count(deps.*)), 0) AS intradep,
                COALESCE(ROUND(sum(deps.late) / count(deps.*)), 0) AS late
            ")
            ->when($this->wnFact, fn ($query) => $query->addSelect(
                DB::raw("COALESCE(ROUND(sum(leads.wn_fact) / count(leads.*)), 0) AS wn_fact")
            ))
            ->when($this->shouldAddBenefit(), function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('COALESCE(ROUND(sum(leads.benefit) / count(leads.*)), 0) AS leads_benefit'),
                    DB::raw('COALESCE(ROUND(sum(deps.benefit) / count(deps.*)), 0) AS deps_benefit'),
                ]);
            })
            ->when($this->groupBy === self::GROUP_OFFICE, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('offices.name as group')])
                    ->from('offices')
                    ->leftJoinSub($this->deposits(), 'deps', 'offices.id', 'deps.office_id')
                    ->leftJoinSub($this->leads(), 'leads', 'offices.id', 'leads.office_id');
            })
            ->when($this->groupBy === self::GROUP_BRANCH, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('branches.name as group')])
                    ->from('branches')
                    ->leftJoinSub($this->deposits(), 'deps', 'branches.id', 'deps.branch_id')
                    ->leftJoinSub($this->leads(), 'leads', 'branches.id', 'leads.branch_id');
            })
            ->when($this->groupBy === self::GROUP_USER, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('users.name as group')])
                    ->from('users')
                    ->leftJoinSub($this->deposits(), 'deps', 'users.id', 'deps.user_id')
                    ->leftJoinSub($this->leads(), 'leads', 'users.id', 'leads.user_id');
            })
            ->when($this->groupBy === self::GROUP_OFFER, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('offers.name as group')])
                    ->from('offers')
                    ->whereIn('offers.id', Offer::allowed()->pluck('id'))
                    ->leftJoinSub($this->deposits(), 'deps', 'offers.id', 'deps.offer_id')
                    ->leftJoinSub($this->leads(), 'leads', 'offers.id', 'leads.offer_id');
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('offices.name as office'),
                ])
                    ->from('offices')
                    ->crossJoin('users')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('users.id', 'deps.user_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('users.id', 'leads.user_id');
                    })
                    ->groupBy(['office']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('offices.name as office'),
                    DB::raw('offers.name as offer'),
                ])
                    ->from('offices')
                    ->crossJoin('users')
                    ->crossJoin('offers')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('users.id', 'deps.user_id')
                            ->on('offers.id', 'deps.offer_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('users.id', 'leads.user_id')
                            ->on('offers.id', 'leads.offer_id');
                    })
                    ->groupBy(['office', 'offer']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offices.name as group'),
                    DB::raw('offers.name as offer'),
                ])
                    ->from('offices')
                    ->crossJoin('offers')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('offers.id', 'deps.offer_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('offers.id', 'leads.offer_id');
                    })
                    ->groupBy(['offer']);
            })
            ->when($this->groupBy === self::GROUP_OFFER_GEO, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offers.name as group'),
                    DB::raw('ip_addresses.country_code as geo'),
                ])
                    ->from('offers')
                    ->crossJoin('ip_addresses')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offers.id', 'deps.offer_id')
                            ->on('ip_addresses.country_code', 'deps.country_code');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offers.id', 'leads.offer_id')
                            ->on('ip_addresses.country_code', 'leads.country_code');
                    })
                    ->groupBy(['geo']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_GEO, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offices.name as group'),
                    DB::raw('offers.name as offer'),
                    DB::raw('phone_lookups.country as geo'),
                ])
                    ->from('offices')
                    ->crossJoin('offers')
                    ->crossJoin('phone_lookups')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('phone_lookups.country', 'deps.country');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('phone_lookups.country', 'leads.country');
                    })
                    ->groupBy(['offer', 'geo']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_DEST, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offices.name as group'),
                    DB::raw('offers.name as offer'),
                    DB::raw('lead_destinations.name as destination'),
                ])
                    ->from('offices')
                    ->crossJoin('offers')
                    ->crossJoin('lead_destinations')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('lead_destinations.id', 'deps.destination_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('lead_destinations.id', 'leads.destination_id');
                    })
                    ->groupBy(['offer', 'destination']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('countries.name as geo'),
                ])
                    ->from('users')
                    ->crossJoin('countries')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('users.id', 'deps.user_id')
                            ->on('countries.code', 'deps.country_code');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('users.id', 'leads.user_id')
                            ->on('countries.code', 'leads.country_code');
                    })
                    ->groupBy(['geo']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO_OFFICE, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('countries.name as geo'),
                    DB::raw('offices.name as office'),
                ])
                    ->from('users')
                    ->crossJoin('countries')
                    ->crossJoin('offices')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('users.id', 'deps.user_id')
                            ->on('countries.code', 'deps.country_code')
                            ->on('offices.id', 'deps.office_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('users.id', 'leads.user_id')
                            ->on('countries.code', 'leads.country_code')
                            ->on('offices.id', 'leads.office_id');
                    })
                    ->groupBy(['geo','office']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER_GEO, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('offices.name as office'),
                    DB::raw('offers.name as offer'),
                    DB::raw('countries.name as geo'),
                ])
                    ->from('offices')
                    ->crossJoin('users')
                    ->crossJoin('offers')
                    ->crossJoin('countries')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('users.id', 'deps.user_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('countries.code', 'deps.country_code');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('users.id', 'leads.user_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('countries.code', 'leads.country_code');
                    })
                    ->groupBy(['office', 'offer', 'geo']);
            })
            ->when($this->groupBy === self::GROUP_DESTINATION, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('lead_destinations.name as group')])
                    ->from('lead_destinations')
                    ->leftJoinSub($this->deposits(), 'deps', 'lead_destinations.id', 'deps.destination_id')
                    ->leftJoinSub($this->leads(), 'leads', 'lead_destinations.id', 'leads.destination_id');
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_PRICE, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offices.name as group'),
                    DB::raw('offers.name as offer'),
                    DB::raw('lead_payment_conditions.model as model'),
                    DB::raw('lead_payment_conditions.cost as cost'),
                ])
                    ->from('offices')
                    ->crossJoin('offers')
                    ->crossJoin('lead_payment_conditions')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('lead_payment_conditions.model', 'deps.model')
                            ->on('lead_payment_conditions.cost', 'deps.cost');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('lead_payment_conditions.model', 'leads.model')
                            ->on('lead_payment_conditions.cost', 'leads.cost');
                    })
                    ->groupBy(['offer', 'lead_payment_conditions.model', 'lead_payment_conditions.cost']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_STATUS, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offices.name as group'),
                    DB::raw('assignments.status'),
                ])
                    ->from(
                        LeadOrderAssignment::selectRaw('distinct status')->whereNotNull('status'),
                        'assignments'
                    )
                    ->crossJoin('offices')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('assignments.status', 'deps.status');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('assignments.status', 'leads.status');
                    })
                    ->groupBy(['assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_STATUS, function (\Illuminate\Database\Query\Builder $builder) {
                $statuses = Status::query()->pluck('name')
                    ->map(function ($item) {
                        return "('" . $item . "')";
                    });

                return $builder
                    ->addSelect([
                        DB::raw('offices.name as group'),
                        DB::raw('offers.name as offer'),
                        DB::raw('statuses.name as status'),
                    ])
                    ->fromRaw("(VALUES " . $statuses->implode(',') . ") statuses(name)")
                    ->crossJoin('offices')
                    ->crossJoin('offers')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offices.id', 'deps.office_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('statuses.name', 'deps.status');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offices.id', 'leads.office_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('statuses.name', 'leads.status');
                    })
                    ->groupBy(['offer', 'statuses.name']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFER_DEST, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('offers.name as offer'),
                    DB::raw('lead_destinations.name as destination'),
                ])
                    ->from('users')
                    ->crossJoin('offers')
                    ->crossJoin('lead_destinations')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('users.id', 'deps.user_id')
                            ->on('offers.id', 'deps.offer_id')
                            ->on('lead_destinations.id', 'deps.destination_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('users.id', 'leads.user_id')
                            ->on('offers.id', 'leads.offer_id')
                            ->on('lead_destinations.id', 'leads.destination_id');
                    })
                    ->groupBy(['offer', 'destination']);
            })
            ->when($this->groupBy === self::GROUP_USER_DEST, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('users.name as group'),
                    DB::raw('lead_destinations.name as destination'),
                ])
                    ->from('users')
                    ->crossJoin('lead_destinations')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('users.id', 'deps.user_id')
                            ->on('lead_destinations.id', 'deps.destination_id');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('users.id', 'leads.user_id')
                            ->on('lead_destinations.id', 'leads.destination_id');
                    })
                    ->groupBy(['destination']);
            })
            ->when($this->groupBy === self::GROUP_APP, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('apps.id as group')])
                    ->fromSub(
                        Lead::selectRaw('distinct app_id as id')->whereNotNull('app_id'),
                        'apps'
                    )
                    ->leftJoinSub($this->deposits(), 'deps', 'apps.id', 'deps.app_id')
                    ->leftJoinSub($this->leads(), 'leads', 'apps.id', 'leads.app_id');
            })
            ->when($this->groupBy === self::GROUP_STATUS, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([DB::raw('assignments.status as group')])
                    ->from(
                        LeadOrderAssignment::selectRaw('distinct status'),
                        'assignments'
                    )
                    ->leftJoinSub($this->deposits(), 'deps', 'assignments.status', 'deps.status')
                    ->leftJoinSub($this->leads(), 'leads', 'assignments.status', 'leads.status');
            })
            ->when($this->groupBy === self::GROUP_OFFER_SOURCE, function (\Illuminate\Database\Query\Builder $builder) {
                return $builder->addSelect([
                    DB::raw('offers.name as group'),
                    DB::raw('traffic_sources.name as traffic_source'),
                ])
                    ->fromSub(
                        Lead::selectRaw('distinct traffic_source as name')->whereNotNull('traffic_source'),
                        'traffic_sources'
                    )
                    ->crossJoin('offers')
                    ->leftJoinSub($this->deposits(), 'deps', function (JoinClause $join) {
                        return $join->on('offers.id', 'deps.offer_id')
                            ->on('traffic_sources.name', 'deps.traffic_source');
                    })
                    ->leftJoinSub($this->leads(), 'leads', function (JoinClause $join) {
                        return $join->on('offers.id', 'leads.offer_id')
                            ->on('traffic_sources.name', 'leads.traffic_source');
                    })
                    ->groupBy(['traffic_sources.name']);
            })
            ->groupBy(['group'])
            ->havingRaw('sum(leads.leads) > 0')
            ->orHavingRaw('sum(deps.deposits) > 0')
            ->get();
    }

    protected function leads()
    {
        return LeadOrderAssignment::visible()
            ->selectRaw("
                count(lead_order_assignments.id) AS leads,
                count(CASE WHEN lead_order_assignments.confirmed_at is not null THEN 1 END) AS confirmed,
                count(CASE WHEN lead_order_assignments.status = 'Неверный номер' THEN 1 END) AS nbt,
                count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) AS duplicate,
                count(CASE WHEN lead_order_assignments.status = 'Меньше 18' THEN 1 END) AS under,
                count(CASE WHEN lead_order_assignments.status in ('Меньше 18', 'Неверный номер', 'Дубль', 'Не резидент', 'Не говорит по-русски', 'На замену') THEN 1 END) AS invalid,
                count(CASE WHEN lead_order_assignments.status in ('Не резидент', 'Не говорит по-русски', 'На замену') THEN 1 END) AS other,
                count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS no_answer
            ")
            ->when($this->wnFact, fn ($query) => $query->addSelect(
                DB::raw("count(CASE WHEN leads.phone_valid = False THEN 1 END) AS wn_fact")
            ))
            ->when(
                !auth()->user()->hasRole([User::ADMIN, User::SUPPORT]) || $this->batches == 'notOnBatch',
                function (Builder $builder) {
                    return $builder->whereNotExists(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->selectRaw('1')
                            ->from('lead_resell_batch')
                            ->whereColumn('lead_resell_batch.lead_id', 'lead_order_assignments.lead_id');
                    });
                }
            )
            ->when($this->batches == 'onBatch', function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('lead_resell_batch')
                        ->whereColumn('lead_resell_batch.lead_id', 'lead_order_assignments.lead_id');
                });
            })
            ->when($this->shouldAddBenefit(), function (Builder $builder) {
                return $builder->addSelect([
                    DB::raw('sum(lead_order_assignments.benefit) as benefit'),
                ]);
            })
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->when($this->groupBy === self::GROUP_OFFICE, function (Builder $builder) {
                return $builder->addSelect(['leads_orders.office_id'])
                    ->groupBy(['leads_orders.office_id']);
            })
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, function (Builder $builder) {
                return $builder->whereNotNull('leads.affiliate_id');
            })
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, function (Builder $builder) {
                return $builder->whereNull('leads.affiliate_id');
            })
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
            ->when($this->groupBy === self::GROUP_BRANCH, function (Builder $builder) {
                return $builder->addSelect(['users.branch_id'])
                    ->join('users', 'leads.user_id', 'users.id')
                    ->groupBy(['users.branch_id']);
            })
            ->when($this->groupBy === self::GROUP_USER, function (Builder $builder) {
                return $builder->addSelect(['leads.user_id'])
                    ->groupBy(['leads.user_id']);
            })
            ->when($this->groupBy === self::GROUP_STATUS, function (Builder $builder) {
                return $builder->addSelect(['lead_order_assignments.status'])
                    ->groupBy(['lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_OFFER, function (Builder $builder) {
                return $builder->addSelect(['lead_order_routes.offer_id'])
                    ->groupBy(['lead_order_routes.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'leads_orders.office_id',
                ])
                    ->groupBy(['leads.user_id', 'leads_orders.office_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                ])
                    ->groupBy(['leads.user_id', 'leads_orders.office_id', 'lead_order_routes.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                ])
                    ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                    'lead_order_assignments.destination_id',
                ])
                    ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id', 'lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'lead_order_routes.offer_id',
                    'ip_addresses.country_code',
                ])
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['lead_order_routes.offer_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                    'phone_lookups.country',
                ])
                    ->leftJoin('phone_lookups', 'leads.phone', 'phone_lookups.phone')
                    ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id', 'phone_lookups.country']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'ip_addresses.country_code',
                ])
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['leads.user_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO_OFFICE, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'ip_addresses.country_code',
                    'leads_orders.office_id'
                ])
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['leads.user_id', 'ip_addresses.country_code','leads_orders.office_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                    'ip_addresses.country_code',
                ])
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['leads.user_id', 'leads_orders.office_id', 'lead_order_routes.offer_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_DESTINATION, function (Builder $builder) {
                return $builder->addSelect(['lead_order_assignments.destination_id'])
                    ->groupBy(['lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_PRICE, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                    'lead_payment_conditions.model',
                    'lead_payment_conditions.cost',
                ])
                    ->leftJoin('lead_payment_conditions', function (JoinClause $joinClause) {
                        return $joinClause->on('leads_orders.office_id', 'lead_payment_conditions.office_id')
                            ->on('lead_order_routes.offer_id', 'lead_payment_conditions.offer_id');
                    })
                    ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id', 'lead_payment_conditions.model', 'lead_payment_conditions.cost']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_STATUS, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_assignments.status',
                ])
                    ->groupBy(['leads_orders.office_id', 'lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_STATUS, function (Builder $builder) {
                return $builder->addSelect([
                    'leads_orders.office_id',
                    'lead_order_routes.offer_id',
                    'lead_order_assignments.status',
                ])
                    ->groupBy(['leads_orders.office_id', 'lead_order_routes.offer_id', 'lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_APP, function (Builder $builder) {
                return $builder->addSelect(['leads.app_id'])
                    ->groupBy(['leads.app_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFER_SOURCE, function (Builder $builder) {
                return $builder->addSelect([
                    'lead_order_routes.offer_id',
                    'leads.traffic_source',
                ])
                    ->groupBy(['lead_order_routes.offer_id', 'leads.traffic_source']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'lead_order_routes.offer_id',
                    'lead_order_assignments.destination_id',
                ])
                    ->groupBy(['leads.user_id',
                        'lead_order_routes.offer_id',
                        'lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'leads.user_id',
                    'lead_order_assignments.destination_id',
                ])
                    ->groupBy(['leads.user_id', 'lead_order_assignments.destination_id']);
            })
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->branches, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'leads.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->users, fn (Builder $query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->offices, fn (Builder $builder) => $builder->whereIn('leads_orders.office_id', $this->offices))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('lead_order_routes.offer_id', $this->offers))
            ->when($this->vertical, fn (Builder $query) => $query->where('offers.vertical', $this->vertical))
            ->when($this->utmSources, fn (Builder $query) => $query->where('leads.utm_source', $this->utmSources))
            ->when(
                $this->offerType === self::OFFER_TYPE_LO,
                fn (Builder $query) => $query->where('offers.name', 'ILIKE', 'LO\_%')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_NOT_LO,
                fn (Builder $query) => $query->where('offers.name', 'NOT ILIKE', 'LO\_%')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_CD,
                fn (Builder $query) => $query->where('offers.name', 'LIKE', '%\_CD')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_NOT_CD,
                fn (Builder $query) => $query->where('offers.name', 'NOT LIKE', '%\_CD')
            )
            ->when($this->destinations, fn (Builder $builder) => $builder->whereIn('lead_order_assignments.destination_id', $this->destinations))
            ->when($this->modelPayment, function ($builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('lead_payment_conditions')
                        ->whereColumn('leads_orders.office_id', 'lead_payment_conditions.office_id')
                        ->whereColumn('lead_order_routes.offer_id', 'lead_payment_conditions.offer_id')
                        ->where('lead_payment_conditions.model', $this->modelPayment);
                });
            })
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            });
    }

    /**
     * @return Deposit|\Illuminate\Database\Concerns\BuildsQueries|Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function deposits()
    {
        return Deposit::visible()
            ->select([
                DB::raw('count(deposits.id) as deposits'),
                DB::raw("count(case when deposits.lead_return_date between '" . $this->since->toDateString() . "' and '" . $this->until->toDateString() . "' then 1 end ) as intradep"),
                DB::raw("count(case when deposits.lead_return_date < '" . $this->since->toDateString() . "' then 1 end ) as late"),
            ])
            ->when(
                !auth()->user()->hasRole([User::ADMIN, User::SUPPORT]) || $this->batches == 'notOnBatch',
                function (Builder $builder) {
                    return $builder->whereNotExists(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->selectRaw('1')
                            ->from('lead_resell_batch')
                            ->whereColumn('lead_resell_batch.lead_id', 'deposits.lead_id');
                    });
                }
            )
            ->when($this->batches == 'onBatch', function (Builder $builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->selectRaw('1')
                        ->from('lead_resell_batch')
                        ->whereColumn('lead_resell_batch.lead_id', 'deposits.lead_id');
                });
            })
            ->when($this->shouldAddBenefit(), function (Builder $builder) {
                return $builder->addSelect([
                    DB::raw("sum(deposits.benefit) as benefit"),
                ]);
            })
            ->join('offers', 'deposits.offer_id', 'offers.id')
            ->join('lead_order_assignments', function (JoinClause $query) {
                return $query->on('deposits.lead_id', 'lead_order_assignments.lead_id')
                    ->on('deposits.lead_return_date', DB::raw('lead_order_assignments.created_at::date'));
            })
            ->when($this->groupBy === self::GROUP_OFFICE, function (Builder $builder) {
                return $builder->addSelect(['deposits.office_id'])
                    ->groupBy(['deposits.office_id']);
            })
            ->when($this->groupBy === self::GROUP_BRANCH, function (Builder $builder) {
                return $builder->addSelect(['users.branch_id'])
                    ->join('users', 'deposits.user_id', 'users.id')
                    ->groupBy(['users.branch_id']);
            })
            ->when($this->groupBy === self::GROUP_STATUS, function (Builder $builder) {
                return $builder->addSelect(['lead_order_assignments.status'])
                    ->groupBy(['lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_USER, function (Builder $builder) {
                return $builder->addSelect(['deposits.user_id'])
                    ->groupBy(['deposits.user_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFER, function (Builder $builder) {
                return $builder->addSelect(['deposits.offer_id'])
                    ->groupBy(['deposits.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'deposits.office_id',
                ])
                    ->groupBy(['deposits.user_id', 'deposits.office_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'deposits.office_id',
                    'deposits.offer_id',
                ])
                    ->groupBy(['deposits.user_id', 'deposits.office_id', 'deposits.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'deposits.offer_id',
                ])
                    ->groupBy(['deposits.office_id', 'deposits.offer_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'deposits.offer_id',
                    'lead_order_assignments.destination_id',
                ])
                    ->groupBy(['deposits.office_id', 'deposits.offer_id', 'lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.offer_id',
                    'ip_addresses.country_code',
                ])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['deposits.offer_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'deposits.offer_id',
                    'phone_lookups.country',
                ])
                    ->leftJoin('phone_lookups', 'deposits.phone', 'phone_lookups.phone')
                    ->groupBy(['deposits.office_id', 'deposits.offer_id', 'phone_lookups.country']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'ip_addresses.country_code',
                ])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['deposits.user_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_USER_GEO_OFFICE, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'ip_addresses.country_code',
                    'deposits.office_id'
                ])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['deposits.user_id', 'ip_addresses.country_code','deposits.office_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFICE_OFFER_GEO, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'deposits.office_id',
                    'deposits.offer_id',
                    'ip_addresses.country_code',
                ])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
                    ->groupBy(['deposits.user_id', 'deposits.office_id', 'deposits.offer_id', 'ip_addresses.country_code']);
            })
            ->when($this->groupBy === self::GROUP_DESTINATION, function (Builder $builder) {
                return $builder->addSelect(['lead_order_assignments.destination_id'])
                    ->groupBy(['lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_PRICE, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'deposits.offer_id',
                    'lead_payment_conditions.model',
                    'lead_payment_conditions.cost',
                ])
                    ->leftJoin('lead_payment_conditions', function (JoinClause $query) {
                        return $query->on('deposits.office_id', 'lead_payment_conditions.office_id')
                            ->on('deposits.offer_id', 'lead_payment_conditions.offer_id');
                    })
                    ->groupBy(['deposits.office_id', 'deposits.offer_id', 'lead_payment_conditions.model', 'lead_payment_conditions.cost']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_STATUS, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'lead_order_assignments.status',
                ])
                    ->groupBy(['deposits.office_id', 'lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_OFFICE_OFFER_STATUS, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.office_id',
                    'deposits.offer_id',
                    'lead_order_assignments.status',
                ])
                    ->groupBy(['deposits.office_id', 'deposits.offer_id', 'lead_order_assignments.status']);
            })
            ->when($this->groupBy === self::GROUP_APP, function (Builder $builder) {
                return $builder->addSelect(['leads.app_id'])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->groupBy(['leads.app_id']);
            })
            ->when($this->groupBy === self::GROUP_OFFER_SOURCE, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.offer_id',
                    'leads.traffic_source',
                ])
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->groupBy(['deposits.offer_id', 'leads.traffic_source']);
            })
            ->when($this->groupBy === self::GROUP_USER_OFFER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'deposits.offer_id',
                    'lead_order_assignments.destination_id'
                ])
                    ->groupBy(['deposits.user_id', 'deposits.offer_id', 'lead_order_assignments.destination_id']);
            })
            ->when($this->groupBy === self::GROUP_USER_DEST, function (Builder $builder) {
                return $builder->addSelect([
                    'deposits.user_id',
                    'lead_order_assignments.destination_id',
                ])
                    ->groupBy(['deposits.user_id', 'lead_order_assignments.destination_id']);
            })
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween('deposits.date', [$this->since, $this->until]);
            })
            ->when($this->branches, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'deposits.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->when($this->teams, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', 'deposits.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->utmSources, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('leads')
                        ->whereColumn('leads.user_id', 'deposits.user_id')
                        ->whereIn('leads.utm_source', $this->utmSources);
                });
            })
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('leads')
                        ->whereColumn('leads.id', 'deposits.lead_id')
                        ->whereNotNull('leads.affiliate_id');
                });
            })
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('leads')
                        ->whereColumn('leads.id', 'deposits.lead_id')
                        ->whereNull('leads.affiliate_id');
                });
            })
            ->when($this->users, fn (Builder $query) => $query->whereIn('deposits.user_id', $this->users))
            ->when($this->offices, fn (Builder $query) => $query->whereIn('deposits.office_id', $this->offices))
            ->when($this->offers, fn (Builder $query) => $query->whereIn('deposits.offer_id', $this->offers))
            ->when($this->vertical, fn (Builder $query) => $query->where('offers.vertical', $this->vertical))
            ->when(
                $this->offerType === self::OFFER_TYPE_LO,
                fn (Builder $query) => $query->where('offers.name', 'ILIKE', 'LO\_%')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_NOT_LO,
                fn (Builder $query) => $query->where('offers.name', 'NOT ILIKE', 'LO\_%')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_CD,
                fn (Builder $query) => $query->where('offers.name', 'LIKE', '%\_CD')
            )
            ->when(
                $this->offerType === self::OFFER_TYPE_NOT_CD,
                fn (Builder $query) => $query->where('offers.name', 'NOT LIKE', '%\_CD')
            )
            ->when($this->destinations, fn (Builder $builder) => $builder->whereIn('lead_order_assignments.destination_id', $this->destinations))
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
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
            ->when($this->modelPayment, function ($builder) {
                return $builder->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('lead_payment_conditions')
                        ->whereColumn('deposits.office_id', 'lead_payment_conditions.office_id')
                        ->whereColumn('deposits.offer_id', 'lead_payment_conditions.offer_id')
                        ->where('lead_payment_conditions.model', $this->modelPayment);
                });
            });
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return $data->map(function ($row) use ($data) {
            $result = ['group' => $row->group];

            if ($this->groupBy === self::GROUP_USER_OFFICE) {
                $result['office'] = $row->office;
            } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER) {
                $result['offer'] = $row->offer;
            } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_DEST) {
                $result['offer']       = $row->offer;
                $result['destination'] = $row->destination;
            } elseif ($this->groupBy === self::GROUP_USER_OFFICE_OFFER) {
                $result['office'] = $row->office;
                $result['offer']  = $row->offer;
            } elseif ($this->groupBy === self::GROUP_OFFER_GEO) {
                $result['geo'] = $row->geo;
            } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_GEO) {
                $result['offer'] = $row->offer;
                $result['geo']   = $row->geo;
            } elseif ($this->groupBy === self::GROUP_USER_GEO) {
                $result['geo'] = $row->geo;
            } elseif ($this->groupBy === self::GROUP_USER_GEO_OFFICE) {
                $result['geo']    = $row->geo;
                $result['office'] = $row->office;
            } elseif ($this->groupBy === self::GROUP_USER_OFFICE_OFFER_GEO) {
                $result['office'] = $row->office;
                $result['offer']  = $row->offer;
                $result['geo']    = $row->geo;
            } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_PRICE) {
                $result['offer'] = $row->offer;
                $result['model'] = $row->model;
                $result['cost']  = $row->cost;
            } elseif ($this->groupBy === self::GROUP_OFFICE_STATUS) {
                $result['status'] = $row->status;
            } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_STATUS) {
                $result['offer'] = $row->offer;
                $result['status'] = $row->status;
            } elseif ($this->groupBy === self::GROUP_OFFER_SOURCE) {
                $result['traffic_source'] = $row->traffic_source;
            } elseif ($this->groupBy === self::GROUP_USER_DEST) {
                $result['destination'] = $row->destination;
            } elseif ($this->groupBy === self::GROUP_USER_OFFER_DEST) {
                $result['offer']       = $row->offer;
                $result['destination'] = $row->destination;
            }

            $result = array_merge($result, [
                'leads'             => $row->leads,
                'confirmed'         => $row->confirmed,
                'wn'                => $row->wn,
                'wn_percent'        => percentage($row->wn, $row->leads),
                'wn_fact'           => $row->wn_fact ?? 0,
                'duplicate'         => $row->duplicate,
                'duplicate_percent' => percentage($row->duplicate, $row->leads),
                'under'             => $row->under,
                'under_percent'     => percentage($row->under, $row->leads),
                'invalid'           => $row->invalid,
                'invalid_percent'   => percentage($row->invalid, $row->leads),
                'other'             => $row->other,
                'other_percent'     => percentage($row->other, $row->leads),
                'no_answer'         => $row->no_answer,
                'no_answer_percent' => percentage($row->no_answer, $row->leads),
                'ftd'               => $row->ftd,
                'intradep'          => $row->intradep,
                'late'              => $row->late,
            ]);

            if ($this->shouldAddBenefit()) {
                $result['benefit'] = $row->leads_benefit + $row->deps_benefit;
            }

            if (!$this->wnFact) {
                unset($result['wn_fact']);
            }

            return $result;
        });
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        $result = ['group' => 'Итого'];

        if ($this->groupBy === self::GROUP_USER_OFFICE) {
            $result['office'] = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER) {
            $result['offer'] = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_DEST) {
            $result['offer']       = '';
            $result['destination'] = '';
        } elseif ($this->groupBy === self::GROUP_USER_OFFICE_OFFER) {
            $result['office'] = '';
            $result['offer']  = '';
        } elseif ($this->groupBy === self::GROUP_OFFER_GEO) {
            $result['geo'] = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_GEO) {
            $result['offer'] = '';
            $result['geo']   = '';
        } elseif ($this->groupBy === self::GROUP_USER_GEO) {
            $result['geo'] = '';
        } elseif ($this->groupBy === self::GROUP_USER_GEO_OFFICE) {
            $result['geo']    = '';
            $result['office'] = '';
        } elseif ($this->groupBy === self::GROUP_USER_OFFICE_OFFER_GEO) {
            $result['office'] = '';
            $result['offer']  = '';
            $result['geo']    = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_PRICE) {
            $result['offer'] = '';
            $result['model'] = '';
            $result['cost']  = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_STATUS) {
            $result['status'] = '';
        } elseif ($this->groupBy === self::GROUP_OFFICE_OFFER_STATUS) {
            $result['offer']  = '';
            $result['status'] = '';
        } elseif ($this->groupBy === self::GROUP_OFFER_SOURCE) {
            $result['traffic_source']  = '';
        } elseif ($this->groupBy === self::GROUP_USER_DEST) {
            $result['destination'] = '';
        } elseif ($this->groupBy === self::GROUP_USER_OFFER_DEST) {
            $result['offer']       = '';
            $result['destination'] = '';
        }

        $result = array_merge($result, [
            'leads'             => $data->sum('leads'),
            'confirmed'         => $data->sum('confirmed'),
            'wn'                => $data->sum('wn'),
            'wn_percent'        => percentage($data->sum('wn'), $data->sum('leads')),
            'wn_fact'           => $data->sum('wn_fact'),
            'duplicate'         => $data->sum('duplicate'),
            'duplicate_percent' => percentage($data->sum('duplicate'), $data->sum('leads')),
            'under'             => $data->sum('under'),
            'under_percent'     => percentage($data->sum('under'), $data->sum('leads')),
            'invalid'           => $data->sum('invalid'),
            'invalid_percent'   => percentage($data->sum('invalid'), $data->sum('leads')),
            'other'             => $data->sum('other'),
            'other_percent'     => percentage($data->sum('other'), $data->sum('leads')),
            'no_answer'         => $data->sum('no_answer'),
            'no_answer_percent' => percentage($data->sum('no_answer'), $data->sum('leads')),
            'ftd'               => $data->sum('ftd'),
            'intradep'          => $data->sum('intradep'),
            'late'              => $data->sum('late'),
        ]);

        if ($this->shouldAddBenefit()) {
            $result['benefit'] = $data->sum(fn ($item) => $item->leads_benefit + $item->deps_benefit);
        }

        if (!$this->wnFact) {
            unset($result['wn_fact']);
        }

        return $result;
    }

    /**
     * Gets headers of the report
     *
     * @return string[]
     */
    protected function headers()
    {
        $result = [];

        foreach (explode('-', $this->groupBy) as $i => $item) {
            $result[$i > 0 ? $item : 'group'] = $item;
        }

        $result = array_merge($result, [
            'leads'             => 'leads',
            'confirmed'         => 'confirmed',
            'wn'                => 'wn',
            'wn_percent'        => 'wn%',
            'wn_fact'           => 'wn (fact)',
            'duplicate'         => 'duplicate',
            'duplicate_percent' => 'duplicate%',
            'under'             => 'under',
            'under_percent'     => 'under%',
            'invalid'           => 'invalid',
            'invalid_percent'   => 'invalid%',
            'other'             => 'other',
            'other_percent'     => 'other%',
            'no_answer'         => 'na',
            'no_answer_percent' => 'na%',
            'ftd'               => 'ftd',
            'intradep'          => 'ftd (period)',
            'late'              => 'ftd (late)',
        ]);

        if ($this->shouldAddBenefit()) {
            $result['benefit'] = 'revenue';
        }

        if (!$this->wnFact) {
            unset($result['wn_fact']);
        }

        return $result;
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
     * @param string $groupBy
     *
     * @return Report
     */
    public function groupBy($groupBy = 'office')
    {
        $this->groupBy = in_array($groupBy, self::GROUP_LIST) ? $groupBy : self::GROUP_OFFICE;

        return $this;
    }

    /**
     * @param array|null $branches
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
     * @param string|null $batches
     *
     * @return Report
     */
    public function forBatches($batches = null)
    {
        if (!empty($batches)) {
            $this->batches = $batches;
        }

        return $this;
    }

    /**
     * @param array|null $teams
     *
     * @return Report
     */
    public function forTeams($teams = null)
    {
        if (!empty($teams)) {
            $this->teams = Arr::wrap($teams);
        }

        return $this;
    }

    /**
     * @param array|null $users
     *
     * @return Report
     */
    public function forUsers($users = null)
    {
        if (!empty($users)) {
            $this->users = Arr::wrap($users);
        }

        return $this;
    }

    /**
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = Office::visible()
            ->when($offices, fn ($q) => $q->whereIn('id', Arr::wrap($offices)))
            ->pluck('id')
            ->push(0)
            ->toArray();

        return $this;
    }

    /**
     * @param null $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = Offer::allowed()
            ->when($offers, fn ($q) => $q->whereIn('id', Arr::wrap($offers)))
            ->pluck('id')
            ->push(0)
            ->toArray();

        return $this;
    }

    /**
     * @param null $vertical
     *
     * @return Report
     */
    public function forVertical($vertical = null)
    {
        $this->vertical = $vertical;

        return $this;
    }

    /**
     * @param null $offerType
     *
     * @return Report
     */
    public function forOfferType($offerType = null)
    {
        $this->offerType = in_array($offerType, self::OFFER_TYPES) ? $offerType : null;

        return $this;
    }

    /**
     * @param null $drivers
     *
     * @return Report
     */
    public function forDrivers($drivers = null)
    {
        if (!empty($drivers)) {
            $this->destinations = LeadDestination::query()
                ->whereIn('driver', Arr::wrap($drivers))
                ->pluck('id')
                ->push(0)
                ->toArray();
        }

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
     * @param bool $wnFact
     *
     * @return Report
     */
    public function forWnFact($wnFact = false)
    {
        $this->wnFact = $wnFact;

        return $this;
    }

    /**
     * @param $trafficType
     *
     * @return Report
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
     * @param null $modelPayment
     *
     * @return Report
     */
    public function forModelPayment($modelPayment = null)
    {
        if ($modelPayment) {
            $this->modelPayment = in_array($modelPayment, LeadPaymentCondition::MODELS) ? $modelPayment : null;
        }

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

    protected function shouldAddBenefit()
    {
        return auth()->user()->displayRevenue() && auth()->user()->branch_id === 16;
    }
}
