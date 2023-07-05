<?php

namespace App\CRM\Queries;

use App\AssignmentDayToDaySnapshot;
use App\CRM\LeadOrderAssignment;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\Manager;
use App\Offer;
use App\Office;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerStatistic
{
    /**
     * @var \App\CRM\LeadOrderAssignment|\Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    protected ?Carbon $since                    = null;
    protected ?Carbon $until                    = null;
    protected ?array $offers                    = null;
    protected ?array $managers                  = null;
    protected ?array $offices                   = null;
    protected ?array $officesWithoutDestination = null;
    protected ?array $typeOffers                = null;
    protected ?array $officeGroups              = null;

    /**
     * ManagerAssignments constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->query = LeadOrderAssignment::query()
            ->where(function ($query) {
                $query->whereNull('deliver_at')
                    ->orWhereNotNull('confirmed_at')
                    ->orWhereNotNull('delivery_failed');
            })
            ->selectRaw(
                "
                count(lead_order_assignments.id) as total,
                count(CASE WHEN lead_order_assignments.status = 'Новый' OR lead_order_assignments.status is null THEN 1 END) AS new,
                count(CASE WHEN lead_order_assignments.status = 'Отказ' THEN 1 END) AS reject,
                count(CASE WHEN lead_order_assignments.status = 'В работе у клоузера' THEN 1 END) AS on_closer,
                count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS no_answer,
                count(CASE WHEN lead_order_assignments.status = 'Дозвонится' THEN 1 END) AS force_call,
                count(CASE WHEN lead_order_assignments.status = 'Демонстрация' THEN 1 END) AS demo,
                count(CASE WHEN lead_order_assignments.status = 'Депозит' THEN 1 END) AS deposits,
                count(CASE WHEN lead_order_assignments.status = 'Перезвон' THEN 1 END) AS callback,
                count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) AS double,
                count(CASE WHEN lead_order_assignments.status = 'Резерв' THEN 1 END) AS reserve,
                count(CASE WHEN lead_order_assignments.status = 'Неверный номер' THEN 1 END) AS wrong_nb,
                round(
                    count(CASE WHEN lead_order_assignments.status = 'Депозит' THEN 1 END)::decimal /
                    NULLIF(
                        count(lead_order_assignments.id) -
                        count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) -
                        count(CASE WHEN lead_order_assignments.status = 'Неверный номер' THEN 1 END),
                        0
                    ) * 100,
                    2
                ) AS conversion,
                count(lead_order_assignments.id) -
                    count(CASE WHEN lead_order_assignments.status = 'Неверный номер' THEN 1 END)  as clean_leads,
                sum(lead_order_routes.\"leadsOrdered\") as ordered
                "
            )
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', '=', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', '=', 'leads_orders.id')
            ->leftJoin('leads', 'lead_order_assignments.lead_id', '=', 'leads.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', '=', 'offers.id')
            ->leftJoin('managers', 'lead_order_routes.manager_id', 'managers.id')
            ->leftJoin('offices', 'managers.office_id', 'offices.id');
    }

    /**
     * Start new query
     *
     * @return static
     */
    public static function query()
    {
        return new static();
    }

    /**
     * Filter for manager
     *
     * @param \App\Manager|null $manager
     *
     * @return \App\CRM\Queries\ManagerStatistic
     */
    public function forManager($manager = null)
    {
        if (!empty($manager)) {
            $this->managers = Arr::wrap($manager);
            $this->query->whereIn('lead_order_routes.manager_id', $this->managers);
        }

        return $this;
    }

    /**
     * Filter stats by offer
     *
     * @param int|null $offer
     *
     * @return \App\CRM\Queries\ManagerStatistic
     */
    public function forOffer($offer = null)
    {
        if ($offer !== null) {
            $this->offers = Arr::wrap($offer);
            $this->query->whereIn('lead_order_routes.offer_id', $this->offers);
        }

        return $this;
    }

    /**
     * Get all results
     *
     * @return \App\CRM\LeadOrderAssignment[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->query->visible()->get();
    }

    /**
     * Scope query to certain office
     *
     * @param null $office
     *
     * @return \App\CRM\Queries\ManagerStatistic
     */
    public function forOffice($office = null)
    {
        if (!empty($office)) {
            $this->offices = Arr::wrap($office);
            $this->query->whereIn('leads_orders.office_id', $this->offices);
        }

        return $this;
    }

    /**
     * @param null $group
     *
     * @return \App\CRM\Queries\ManagerStatistic
     */
    public function forOfficeGroup($group = null)
    {
        if (!empty($group)) {
            $this->officeGroups = Arr::wrap($group);

            $this->query->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                return $query->selectRaw('1')
                    ->from('office_office_group')
                    ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                    ->whereIn('office_office_group.group_id', $this->officeGroups);
            });
        }

        return $this;
    }

    /**
     * Since when
     *
     * @param null $date
     *
     * @return $this
     */
    public function since($date = null)
    {
        if ($date) {
            $this->since = Carbon::parse($date);
            $this->query->whereDate('lead_order_assignments.created_at', '>=', $this->since->toDateString());
        }

        return $this;
    }

    /**
     * Until when
     *
     * @param null $date
     *
     * @return $this
     */
    public function until($date = null)
    {
        if ($date) {
            $this->until = Carbon::parse($date);
            $this->query->whereDate('lead_order_assignments.created_at', '<=', $this->until->toDateString());
        }

        return $this;
    }

    /**
     * Since when
     *
     * @param null $date
     *
     * @return $this
     */
    public function sinceRegistration($date = null)
    {
        if ($date) {
            $this->query->whereDate('lead_order_assignments.registered_at', '>=', Carbon::parse($date)->toDateString());
        }

        return $this;
    }

    /**
     * Until when
     *
     * @param null $date
     *
     * @return $this
     */
    public function untilRegistration($date = null)
    {
        if ($date) {
            $this->query->whereDate('lead_order_assignments.registered_at', '<=', Carbon::parse($date)->toDateString());
        }

        return $this;
    }

    /**
     * Scope offer by its type. Omg, this is dumb af, delete once we done.
     *
     * @param $input
     *
     * @return \App\CRM\Queries\ManagerStatistic
     */
    public function forOfferType($input)
    {
        if ($input !== null) {
            $this->typeOffers = $input === 'current'
                ? Offer::current()->pluck('id')->toArray()
                : Offer::skipsConversion()->pluck('id')->toArray();

            $this->query->whereIn('lead_order_routes.offer_id', $this->typeOffers);
        }

        return $this;
    }

    /**
     * Filter results by branch
     *
     * @param int|array|null $branch
     *
     * @return $this
     */
    public function forBranch($branch = null)
    {
        if (!empty($branch)) {
            $userIds = User::withTrashed()->whereIn('branch_id', Arr::wrap($branch))->pluck('id');
            $this->query->whereIn('leads.user_id', $userIds);
        }

        return $this;
    }

    /**
     * Exclude stats with office's destinations
     *
     * @return $this
     */
    public function withoutOfficeDestination()
    {
        $this->officesWithoutDestination = Office::where(function ($query) {
            return $query->whereNull('destination_id')
                ->orWhereIn('destination_id', LeadDestination::whereIn('driver', [LeadDestinationDriver::INVESTEX, LeadDestinationDriver::HOTLEADS])->pluck('id'));
        })->pluck('id')->values()->toArray();

        $this->query->whereIn('leads_orders.office_id', $this->officesWithoutDestination);

        return $this;
    }

    /**
     * @return $this
     */
    public function groupByOffer()
    {
        $this->query
            ->addSelect([
                'offers.name',
                DB::raw('coalesce(dtd.total, 0) as dtd_total'),
                DB::raw('coalesce(dtd.deposits, 0) as dtd_deposits'),
                DB::raw('coalesce(dtd.no_answer, 0) as dtd_no_answer'),
            ])
            ->leftJoinSub(
                $this->dayToDaySnapshots()
                    ->addSelect('offer_id')
                    ->groupBy('offer_id'),
                'dtd',
                'lead_order_routes.offer_id',
                '=',
                'dtd.offer_id'
            )
            ->groupBy(['offers.name', 'dtd.total', 'dtd.deposits', 'dtd.no_answer']);

        return $this;
    }

    /**
     * @return $this
     */
    public function groupByManager()
    {
        $this->query
            ->addSelect([
                'managers.name',
                DB::raw('coalesce(dtd.total, 0) as dtd_total'),
                DB::raw('coalesce(dtd.deposits, 0) as dtd_deposits'),
                DB::raw('coalesce(dtd.no_answer, 0) as dtd_no_answer'),
            ])
            ->leftJoinSub(
                $this->dayToDaySnapshots()
                    ->addSelect('manager_id')
                    ->groupBy('manager_id'),
                'dtd',
                'lead_order_routes.manager_id',
                '=',
                'dtd.manager_id'
            )
            ->groupBy(['managers.name', 'dtd.total', 'dtd.deposits', 'dtd.no_answer']);

        return $this;
    }

    /**
     * @return $this
     */
    public function groupByOffice()
    {
        $this->query
            ->addSelect([
                'offices.name',
                DB::raw('coalesce(dtd.total, 0) as dtd_total'),
                DB::raw('coalesce(dtd.deposits, 0) as dtd_deposits'),
                DB::raw('coalesce(dtd.no_answer, 0) as dtd_no_answer'),
            ])
            ->leftJoinSub(
                $this->dayToDaySnapshots()
                    ->join('managers', 'assignment_day_to_day_snapshots.manager_id', 'managers.id')
                    ->addSelect('managers.office_id')
                    ->groupBy('managers.office_id'),
                'dtd',
                'offices.id',
                '=',
                'dtd.office_id'
            )
            ->groupBy(['offices.name', 'dtd.total', 'dtd.deposits', 'dtd.no_answer']);

        return $this;
    }

    /**
     * @param string|null $column
     * @param bool        $descending
     *
     * @return $this
     */
    public function orderBy($column, $descending = false)
    {
        $this->query->orderBy($column ?: 'name', $descending ? 'desc' : 'asc');

        return $this;
    }

    /**
     * @return AssignmentDayToDaySnapshot|\Illuminate\Database\Concerns\BuildsQueries|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|mixed
     */
    protected function dayToDaySnapshots()
    {
        $days = $this->until->diffInDays($this->since) + 1;

        $managers = Manager::query()
            ->when($this->offices, fn ($query) => $query->whereIn('office_id', $this->offices))
            ->when($this->officeGroups, function ($query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('managers.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', $this->officeGroups);
                });
            })
            ->when(
                $this->officesWithoutDestination !== null,
                fn ($query) => $query->whereIn('office_id', $this->officesWithoutDestination)
            )
            ->when($this->managers, fn ($query) => $query->whereIn('id', $this->managers))
            ->pluck('id')
            ->values();

        return AssignmentDayToDaySnapshot::query()
            ->selectRaw("
                sum(total)::decimal / $days as total,
                sum(deposits)::decimal / $days as deposits,
                sum(no_answer)::decimal / $days as no_answer
            ")
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->when($this->offers, fn ($query) => $query->whereIn('offer_id', $this->offers))
            ->when($this->typeOffers, fn ($query) => $query->whereIn('offer_id', $this->typeOffers))
            ->whereIn('manager_id', $managers);
    }
}
