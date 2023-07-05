<?php

namespace App\Reports\LeadStats;

use App\Lead;
use App\LeadOrderAssignment;
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
 * @package App\Reports\LeadStats
 */
class Report implements Responsable, Arrayable
{
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
     * Determines if to group by office
     *
     * @var boolean
     */
    protected $groupByOffice;

    /**
     * Determines if to group by offer
     *
     * @var boolean
     */
    protected $groupByOffer;

    /**
     * 56639 - 13971
     *
     * @var array|null
     */
    protected $offices;

    /**
     * @var |null
     */
    protected $status;
    /**
     * @var |null
     */
    protected $offers;

    /**
     * @var mixed|null
     */
    protected $traffic;

    /**
     * @var array|null
     */
    protected $users;

    /**
     * @var string|null
     */
    protected $utmCampaign;

    /**
     * @var string|null
     */
    protected $utmContent;

    /**
     * @var string|null
     */
    protected $utmSource;

    /**
     * @var array|null
     */
    protected $affiliates;

    /**
     * @var array|null
     */
    protected $labels;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $markers;

    /**
     * @var array|null
     */
    protected $countries;

    /**
     * @var boolean
     */
    protected $groupByGeo;

    /**
     * @var boolean
     */
    protected $groupByUtmSource;

    /**
     * @var array|null
     */
    protected $officeGroups;

    /**
     * @var array|null
     */
    protected $branches;

    /**
     * @var string|null
     */
    protected ?string $trafficSource = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\LeadStats\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'            => $request->get('since'),
            'until'            => $request->get('until'),
            'groupBy'          => $request->get('groupBy'),
            'offices'          => $request->get('offices'),
            'status'           => $request->get('status'),
            'offers'           => $request->get('offers'),
            'groupByOffice'    => $request->boolean('groupByOffice', true),
            'groupByOffer'     => $request->boolean('groupByOffer', true),
            'traffic'          => $request->get('traffic'),
            'users'            => $request->get('users'),
            'utmCampaign'      => $request->get('utmCampaign'),
            'utmContent'       => $request->get('utmContent'),
            'utmSource'        => $request->get('utmSource'),
            'affiliates'       => $request->get('affiliates'),
            'labels'           => $request->get('labels'),
            'markers'          => $request->get('markers'),
            'countries'        => $request->get('countries'),
            'groupByGeo'       => $request->boolean('groupByGeo', true),
            'groupByUtmSource' => $request->boolean('groupByUtmSource', true),
            'officeGroups'     => $request->get('officeGroups'),
            'branches'         => $request->get('branches'),
            'traffic_source'   => $request->get('traffic_source'),
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
            ->forOffices($settings['offices'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->forStatus($settings['status'])
            ->groupByOffice($settings['groupByOffice'] ?? true)
            ->groupByOffer($settings['groupByOffer'] ?? true)
            ->forTraffic($settings['traffic'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forUtmCampaign($settings['utmCampaign'] ?? null)
            ->forUtmContent($settings['utmContent'] ?? null)
            ->forUtmSource($settings['utmSource'] ?? null)
            ->forAffiliates($settings['affiliates'] ?? null)
            ->forLabels($settings['labels'] ?? null)
            ->forMarkers($settings['markers'] ?? null)
            ->forCountries($settings['countries'] ?? null)
            ->groupByGeo($settings['groupByGeo'] ?? true)
            ->groupByUtmSource($settings['groupByUtmSource'] ?? true)
            ->forOfficeGroups($settings['officeGroups'] ?? null)
            ->forBranches($settings['branches'] ?? null)
            ->forTrafficSource($settings['traffic_source'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\LeadStats\Report
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
     * @return \App\Reports\LeadStats\Report
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
    public function groupBy($groupBy = 'status')
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = $offices;

        return $this;
    }

    /**
     * @param null $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

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
     * @param string $groupBy
     * @param mixed  $groupByOffice
     *
     * @return Report
     */
    public function groupByOffice($groupByOffice = true)
    {
        $this->groupByOffice = $groupByOffice;

        return $this;
    }

    /**
     * @param mixed $groupByOffer
     *
     * @return Report
     */
    public function groupByOffer($groupByOffer = true)
    {
        $this->groupByOffer = $groupByOffer;

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
        $data = $this->aggregate();

        return [
            'headers'  => $this->headers(),
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Lead[]|array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::visible()->allowedOffers()
            ->select([
                DB::raw('count(distinct lead_order_assignments.id) AS leads'),
                DB::raw('count(distinct deposits.id) AS deposits'),
            ])
            ->when($this->groupByOffice, fn ($query) => $query->addSelect('offices.name as office'))
            ->when($this->groupByOffer, function ($query) {
                return $query->when(
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
                    );
            })
            ->when($this->groupByGeo, fn ($query) => $query->addSelect('ip_addresses.country_name as geo'))
            ->when($this->groupByUtmSource, fn ($query) => $query->addSelect('leads.utm_source as utm_source'))
            ->when($this->groupBy === 'status', fn ($query) => $query->addSelect(DB::raw("COALESCE(lead_order_assignments.status, 'Новый') as lead_group")))
            ->when($this->groupBy === 'note', fn ($query) => $query->addSelect(DB::raw('lead_order_assignments.reject_reason as lead_group')))
            ->when($this->groupBy === 'age', fn ($query) => $query->addSelect(DB::raw('lead_order_assignments.age as lead_group')))
            ->when($this->groupBy === 'profession', fn ($query) => $query->addSelect(DB::raw('lead_order_assignments.profession as lead_group')))
            ->when($this->groupBy === 'gender-age', function ($query) {
                $query->addSelect([
                    DB::raw('lead_order_assignments.age as lead_group'),
                    'lead_order_assignments.gender_id',
                ]);
            })
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('offices', 'leads_orders.office_id', 'offices.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', 'offers.id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->leftJoin('ip_addresses', 'leads.ip', 'ip_addresses.ip')
            ->when($this->traffic === 'own', function ($query) {
                return $query->whereNull('leads.affiliate_id');
            })
            ->when($this->traffic === 'affiliate', function ($query) {
                return $query->whereNotNull('leads.affiliate_id');
            })
            ->when($this->affiliates, fn ($query) => $query->whereIn('leads.affiliate_id', $this->affiliates))
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offices, fn ($query) => $query->whereIn('offices.id', $this->offices))
            ->when($this->offers, fn ($query) => $query->whereIn('lead_order_routes.offer_id', $this->offers))
            ->when($this->countries, fn ($query) => $query->whereIn('ip_addresses.country', $this->countries))
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->utmCampaign, fn ($query) => $query->where('leads.utm_campaign', $this->utmCampaign))
            ->when($this->utmContent, fn ($query) => $query->where('leads.utm_content', $this->utmContent))
            ->when($this->utmSource, fn ($query) => $query->where('leads.utm_source', $this->utmSource))
            ->when($this->labels, function (Builder $builder) {
                return $builder->whereHas('labels', fn ($query) => $query->whereIn('labels.id', $this->labels));
            })
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->when($this->branches, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->when($this->groupByOffice, fn ($query) => $query->groupBy('offices.name'))
            ->when($this->groupByOffer, fn ($query) => $query->groupBy('offer'))
            ->when($this->groupByGeo, fn ($query) => $query->groupBy('ip_addresses.country_name'))
            ->when($this->groupByUtmSource, fn ($query) => $query->groupBy('leads.utm_source'))
            ->when($this->groupBy === 'gender-age', function ($query) {
                $query->groupBy(['lead_order_assignments.gender_id', 'lead_group']);
            })
            ->unless($this->groupBy === 'gender-age', fn ($query) => $query->groupBy('lead_group'))
            ->when($this->markers, function (Builder $builder) {
                $builder->whereHas('lead', function (Builder $leadQuery) {
                    $leadQuery->whereHas('markers', fn (Builder $markerQuery) => $markerQuery->whereIn('name', $this->markers));
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
            ->get();
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return $data
            ->map(function ($row) use ($data) {
                $all = $data->where('office', $row->office)->sum('leads');

                if ($this->groupBy === 'gender-age') {
                    $result = [
                        'office'     => $row->office,
                        'offer'      => $row->offer,
                        'geo'        => $row->geo,
                        'utm_source' => $row->utm_source,
                        'gender'     => $row->gender,
                        'age'        => $row->lead_group,
                        'leads'      => $row->leads,
                        'deposits'   => $row->deposits,
                        'percentage' => sprintf("%s%%", percentage($row->leads, $all)),
                        'conversion' => sprintf("%s%%", percentage($row->deposits, $row->leads)),
                    ];
                } else {
                    $result = [
                        'office'     => $row->office,
                        'offer'      => $row->offer,
                        'geo'        => $row->geo,
                        'utm_source' => $row->utm_source,
                        'lead_group' => $row->lead_group,
                        'leads'      => $row->leads,
                        'deposits'   => $row->deposits,
                        'percentage' => sprintf("%s%%", percentage($row->leads, $all)),
                        'conversion' => sprintf("%s%%", percentage($row->deposits, $row->leads)),
                    ];
                }

                if (!$this->groupByOffice) {
                    unset($result['office']);
                }
                if (!$this->groupByOffer) {
                    unset($result['offer']);
                }
                if (!$this->groupByGeo) {
                    unset($result['geo']);
                }
                if (!$this->groupByUtmSource) {
                    unset($result['utm_source']);
                }

                return $result;
            })->when($this->status && $this->groupBy !== 'gender-age', function ($collection) {
                return $collection->filter(fn ($row) => $row['lead_group'] == $this->status);
            });
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        if ($this->groupBy === 'gender-age') {
            $result = [
                'office'     => 'Итого',
                'offer'      => !$this->groupByOffice ? 'Итого' : '',
                'geo'        => !$this->groupByOffice && !$this->groupByOffer ? 'Итого' : '',
                'utm_source' => !$this->groupByOffice && !$this->groupByOffer && !$this->groupByGeo ? 'Итого' : '',
                'gender'     => (!$this->groupByOffice && !$this->groupByOffer && !$this->groupByGeo && !$this->groupByUtmSource) ? 'Итого' : '',
                'age'        => '',
                'leads'      => $data->sum('leads'),
                'deposits'   => $data->sum('deposits'),
                'percentage' => '100%',
                'conversion' => sprintf("%s%%", percentage($data->sum('deposits'), $data->sum('leads'))),
            ];
        } else {
            $result = [
                'office'                                 => 'Итого',
                'offer'                                  => !$this->groupByOffice ? 'Итого' : '',
                'geo'                                    => !$this->groupByOffice && !$this->groupByOffer ? 'Итого' : '',
                'utm_source'                             => !$this->groupByOffice && !$this->groupByOffer && !$this->groupByGeo ? 'Итого' : '',
                'lead_group'                             => (!$this->groupByOffice && !$this->groupByOffer && !$this->groupByGeo && !$this->groupByUtmSource) ? 'Итого' : '',
                'leads'                                  => $data->when($this->status, function ($collection) {
                    return $collection->filter(fn ($row) => $row['lead_group'] == $this->status);
                })->sum('leads'),
                'deposits'                               => $data->when($this->status, function ($collection) {
                    return $collection->filter(fn ($row) => $row['lead_group'] == $this->status);
                })->sum('deposits'),
                'percentage'                             => percentage($data->when($this->status, function ($collection) {
                    return $collection->filter(fn ($row) => $row['lead_group'] == $this->status);
                })->sum('leads'), $data->sum('leads')),
                'conversion' => sprintf("%s%%", percentage(
                    $data->when(
                        $this->status,
                        fn ($c) => $c->filter(fn ($row) => $row['lead_group'] == $this->status)
                    )->sum('deposits'),
                    $data->when(
                        $this->status,
                        fn ($c) => $c->filter(fn ($row) => $row['lead_group'] == $this->status)
                    )->sum('leads')
                )),
            ];
        }

        if (!$this->groupByOffice) {
            unset($result['office']);
        }
        if (!$this->groupByOffer) {
            unset($result['offer']);
        }
        if (!$this->groupByGeo) {
            unset($result['geo']);
        }
        if (!$this->groupByUtmSource) {
            unset($result['utm_source']);
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
        if ($this->groupBy === 'gender-age') {
            $result = ['gender', 'age', 'leads', 'deposits', '%', 'conversion'];
        } else {
            $result = ['group', 'leads', 'deposits', '%', 'conversion'];
        }

        if ($this->groupByUtmSource) {
            array_unshift($result, 'utm_source');
        }
        if ($this->groupByGeo) {
            array_unshift($result, 'geo');
        }
        if ($this->groupByOffer) {
            array_unshift($result, 'offer');
        }
        if ($this->groupByOffice) {
            array_unshift($result, 'office');
        }

        return  $result;
    }

    /**
     * Filter by status
     *
     * @param null $status
     *
     * @return \App\Reports\LeadStats\Report
     */
    protected function forStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    protected function forTraffic($traffic = null)
    {
        $this->traffic = $traffic;

        return $this;
    }

    /**
     * @param array|null $users
     *
     * @return Report
     */
    public function forUsers($users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @param string|null $utmCampaign
     *
     * @return \App\Reports\LeadStats\Report
     */
    protected function forUtmCampaign($utmCampaign = null)
    {
        $this->utmCampaign = $utmCampaign;

        return $this;
    }

    /**
     * @param string|null $utmContent
     *
     * @return \App\Reports\LeadStats\Report
     */
    protected function forUtmContent($utmContent = null)
    {
        $this->utmContent = $utmContent;

        return $this;
    }

    /**
     * @param string|null $utmSource
     *
     * @return \App\Reports\LeadStats\Report
     */
    protected function forUtmSource($utmSource = null)
    {
        $this->utmSource = $utmSource;

        return $this;
    }

    /**
     * @param array|null $affiliates
     *
     * @return Report
     */
    public function forAffiliates($affiliates = null)
    {
        $this->affiliates = $affiliates;

        return $this;
    }

    /**
     * @param array|null $labels
     *
     * @return Report
     */
    public function forLabels($labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param array|null $labels
     * @param null|mixed $markers
     *
     * @return Report
     */
    public function forMarkers($markers = null)
    {
        $this->markers = $markers;

        return $this;
    }

    /**
     * @param null $countries
     *
     * @return Report
     */
    public function forCountries($countries = null)
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * @param bool $groupByGeo
     *
     * @return Report
     */
    public function groupByGeo(bool $groupByGeo = true)
    {
        $this->groupByGeo = $groupByGeo;

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
}
