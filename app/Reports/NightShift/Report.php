<?php

namespace App\Reports\NightShift;

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
 * @package App\Reports\NightShift
 */
class Report implements Responsable, Arrayable
{
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
     * @var array|null
     */
    protected $affiliates;

    /**
     * @var array|null
     */
    protected $labels;

    /**
     * @var array|null
     */
    protected $officeGroups;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\NightShift\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'           => $request->get('since'),
            'until'           => $request->get('until'),
            'groupBy'         => $request->get('groupBy'),
            'offices'         => $request->get('offices'),
            'status'          => $request->get('status'),
            'offers'          => $request->get('offers'),
            'groupByOffice'   => $request->boolean('groupByOffice', true),
            'traffic'         => $request->get('traffic'),
            'users'           => $request->get('users'),
            'utmCampaign'     => $request->get('utmCampaign'),
            'utmContent'      => $request->get('utmContent'),
            'affiliates'      => $request->get('affiliates'),
            'labels'          => $request->get('labels'),
            'officeGroups'    => $request->get('officeGroups'),
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
            ->forTraffic($settings['traffic'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forUtmCampaign($settings['utmCampaign'] ?? null)
            ->forUtmContent($settings['utmContent'] ?? null)
            ->forAffiliates($settings['affiliates'] ?? null)
            ->forLabels($settings['labels'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\NightShift\Report
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
     * @return \App\Reports\NightShift\Report
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
        return LeadOrderAssignment::query()
            ->select([
                DB::raw('offers.name as offer'),
                DB::raw('count(lead_order_assignments.id) AS leads'),
                DB::raw('count(deposits.id) AS deposits'),
            ])
            ->when($this->groupByOffice, fn ($query) => $query->addSelect('offices.name as office'))
            ->when($this->groupBy === 'status', fn ($query) => $query->addSelect(DB::raw('lead_order_assignments.status as lead_group')))
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
            ->leftJoin('offers', 'leads.offer_id', 'offers.id')
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('offices', 'leads_orders.office_id', 'offices.id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->when($this->traffic === 'own', function ($query) {
                return $query->whereNull('leads.affiliate_id');
            })
            ->when($this->traffic === 'affiliate', function ($query) {
                return $query->whereNotNull('leads.affiliate_id');
            })
            ->when($this->affiliates, fn ($query) => $query->whereIn('leads.affiliate_id', $this->affiliates))
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('lead_order_assignments.registered_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offices, fn ($query) => $query->whereIn('offices.id', $this->offices))
            ->whereIn('lead_order_routes.offer_id', [40,391])
            ->when($this->offers, fn ($query) => $query->whereIn('offers.id', $this->offers))
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->utmCampaign, fn ($query) => $query->where('leads.utm_campaign', $this->utmCampaign))
            ->when($this->utmContent, fn ($query) => $query->where('leads.utm_content', $this->utmContent))
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
            ->when($this->groupByOffice, fn ($query) => $query->groupBy('offices.name'))
            ->when($this->groupBy === 'gender-age', function ($query) {
                $query->groupBy(['offer', 'lead_order_assignments.gender_id', 'lead_group']);
            })
            ->unless($this->groupBy === 'gender-age', fn ($query) => $query->groupBy(['offer', 'lead_group']))
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
                $all = $data->where('office', $row->office)
                    ->where('offer', $row->offer)
                    ->sum('leads');

                if ($this->groupBy === 'gender-age') {
                    $result = [
                        'office'     => $row->office,
                        'offer'      => $row->offer,
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
                        'lead_group' => $row->lead_group,
                        'leads'      => $row->leads,
                        'deposits'   => $row->deposits,
                        'percentage' => sprintf("%s%%", percentage($row->leads, $all)),
                        'conversion' => sprintf("%s%%", percentage($row->deposits, $row->leads)),
                    ];
                }

                if (!$this->groupByOffice) {
                    array_shift($result);
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
                'gender'     => '',
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
                'lead_group'                             => '',
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
            array_shift($result);
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
            $result = ['office', 'offer', 'gender', 'age', 'leads', 'deposits', '%', 'conversion'];
        } else {
            $result = ['office', 'offer', 'group', 'leads', 'deposits', '%', 'conversion'];
        }

        if (!$this->groupByOffice) {
            array_shift($result);
        }

        return  $result;
    }

    /**
     * Filter by status
     *
     * @param null $status
     *
     * @return \App\Reports\NightShift\Report
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
     * @return \App\Reports\NightShift\Report
     */
    protected function forUtmCampaign($utmCampaign = null)
    {
        $this->utmCampaign = $utmCampaign;

        return $this;
    }

    /**
     * @param string|null $utmContent
     *
     * @return \App\Reports\NightShift\Report
     */
    protected function forUtmContent($utmContent = null)
    {
        $this->utmContent = $utmContent;

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
}
