<?php

namespace App\Reports\OfficeAffiliatePerformance;

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
     * Affiliates used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $affiliates;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $officeGroups;

    /**
     * @var Deposit[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $results;

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
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'offices'      => $request->get('offices'),
            'offers'       => $request->get('offers'),
            'branches'     => $request->get('branches'),
            'affiliates'   => $request->get('affiliates'),
            'officeGroups' => $request->get('officeGroups'),
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
            ->forAffiliates($settings['affiliates'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null);
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
     * Filter results for specific branches
     *
     * @param array|string $affiliates
     *
     * @return Report
     */
    public function forAffiliates($affiliates = null)
    {
        if (!empty($affiliates)) {
            $this->affiliates = Arr::wrap($affiliates);
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
            'headers'  => Headers::ALL,
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
            ->join('affiliates', 'leads.affiliate_id', 'affiliates.id')
            ->select([
                DB::raw('affiliates.name as affiliate'),
                DB::raw('offices.name as office'),
                DB::raw('offers.name as offer'),
                DB::raw('count(distinct lead_order_assignments.id) as leads'),
                DB::raw('count(distinct deposits.id) as deposits'),
                DB::raw('COALESCE(late_deposits.cnt, 0) as late_deposits'),
                DB::raw('(CASE WHEN count(distinct lead_order_assignments.id) > 0 THEN (count(distinct deposits.id) * 100 / count(distinct lead_order_assignments.id))::decimal ELSE 0 END) as conversion'),
                DB::raw('count(distinct CASE WHEN lead_order_assignments.status = \'Нет ответа\' THEN lead_order_assignments.id END) AS no_answer'),
                DB::raw('count(distinct
                    CASE WHEN
                        lead_order_assignments.status = \'Неверный номер\' OR
                        lead_order_assignments.status = \'Дубль\' OR
                        leads.valid = false OR
                        leads.phone_valid = false
                    THEN lead_order_assignments.id END
                ) AS invalid'),
                DB::raw('count(distinct
                    CASE WHEN
                        lead_order_assignments.status = \'Новый\' OR
                        lead_order_assignments.status = \'\' OR
                        lead_order_assignments.status is null
                    THEN lead_order_assignments.id END
                ) AS new'),
            ])
            ->leftJoinSub($this->lateDeposits(), 'late_deposits', function (JoinClause $joinClause) {
                return $joinClause->on('affiliates.name', 'late_deposits.affiliate')
                    ->on('offices.name', 'late_deposits.office')
                    ->on('offers.name', 'late_deposits.offer');
            })
            ->when($this->affiliates, fn ($query) => $query->whereIn('leads.affiliate_id', $this->affiliates))
            ->when($this->branches, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'leads.user_id')
                        ->whereIn('users.branch_id', $this->branches);
                });
            })
            ->whereIn('offices.id', $this->offices)
            ->whereIn('lead_order_routes.offer_id', $this->offers)
            ->when($this->officeGroups, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('offices.id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->whereBetween('lead_order_assignments.created_at', [
                $this->since->startOfDay()->toDateTimeString(),
                $this->until->endOfDay()->toDateTimeString()
            ])
            ->groupBy('affiliates.name', 'offices.name', 'offers.name', 'late_deposits.cnt')
            ->orderBy('conversion')
            ->get();
    }

    public function lateDeposits()
    {
        return Deposit::visible()
            ->select([
                DB::raw('count(distinct deposits.id) as cnt'),
                DB::raw('affiliates.name as affiliate'),
                DB::raw('offices.name as office'),
                DB::raw('offers.name as offer'),
            ])
            ->join('offices', 'deposits.office_id', 'offices.id')
            ->join('offers', 'deposits.offer_id', 'offers.id')
            ->join('leads', 'deposits.lead_id', 'leads.id')
            ->leftJoin('affiliates', 'leads.affiliate_id', 'affiliates.id')
            ->whereIn('deposits.office_id', $this->offices)
            ->whereIn('deposits.offer_id', $this->offers)
            ->when($this->officeGroups, function (\Illuminate\Database\Eloquent\Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
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
            ->groupBy(['affiliates.name', 'offices.name', 'offers.name']);
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function rows()
    {
        return $this->results->map(function ($row) {
            return [
                Fields::AFFILIATE            => $row->affiliate,
                Fields::OFFICE               => $row->office,
                Fields::OFFER                => $row->offer,
                Fields::LEADS                => $row->leads ?: 0,
                Fields::DEPOSITS             => $row->deposits ?: 0,
                Fields::CONVERSION           => percentage($row->deposits, $row->leads),
                Fields::LATE_DEPOSITS        => $row->late_deposits ?: 0,
                Fields::LATE_CONVERSION      => percentage($row->late_deposits, $row->leads),
                Fields::TOTAL_DEPOSITS       => $row->late_deposits + $row->deposits,
                Fields::TOTAL_CONVERSION     => percentage($row->late_deposits + $row->deposits, $row->leads),
                Fields::NEW                  => $row->new,
                Fields::NO_ANSWER            => $row->no_answer,
                Fields::NO_ANSWER_CONVERSION => percentage($row->no_answer, $row->leads),
                Fields::INVALID              => $row->invalid,
                Fields::INVALID_CONVERSION   => percentage($row->invalid, $row->leads),
            ];
        });
    }

    /**
     * @return array
     */
    protected function summary()
    {
        return [
            Fields::AFFILIATE            => 'Итого',
            Fields::OFFICE               => '',
            Fields::OFFER                => '',
            Fields::LEADS                => $this->results->sum('leads') ?: 0,
            Fields::DEPOSITS             => $this->results->sum('deposits') ?: 0,
            Fields::CONVERSION           => percentage($this->results->sum('deposits'), $this->results->sum('leads')),
            Fields::LATE_DEPOSITS        => $this->results->sum('late_deposits'),
            Fields::LATE_CONVERSION      => percentage($this->results->sum('late_deposits'), $this->results->sum('leads')),
            Fields::TOTAL_DEPOSITS       => $this->results->sum('late_deposits') + $this->results->sum('deposits'),
            Fields::TOTAL_CONVERSION     => percentage($this->results->sum('late_deposits') + $this->results->sum('deposits'), $this->results->sum('leads')),
            Fields::NEW                  => $this->results->sum('new'),
            Fields::NO_ANSWER            => $this->results->sum('no_answer'),
            Fields::NO_ANSWER_CONVERSION => percentage($this->results->sum('no_answer'), $this->results->sum('leads')),
            Fields::INVALID              => $this->results->sum('invalid'),
            Fields::INVALID_CONVERSION   => percentage($this->results->sum('invalid'), $this->results->sum('leads')),
        ];
    }
}
