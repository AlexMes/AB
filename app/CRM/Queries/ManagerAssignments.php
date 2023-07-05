<?php

namespace App\CRM\Queries;

use App\CRM\Callback;
use App\CRM\LeadOrderAssignment;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManagerAssignments
{
    /**
     * @var \App\CRM\LeadOrderAssignment|\Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * ManagerAssignments constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->query = LeadOrderAssignment::query()
            ->with(['scheduledCallbacks'])
            ->select([
                'lead_order_assignments.id',
                'lead_order_assignments.timezone',
                'lead_order_assignments.registered_at',
                'offers.name',
                'leads.email',
                'leads.uuid',
                'leads.utm_content',
                DB::raw('managers.name as manager'),
                'leads.phone',
                'leads.ip',
                DB::raw('ip_addresses.timezone as registration_timezone'),
                DB::raw("CONCAT(leads.firstname,' ',leads.middlename,' ',leads.lastname) as fullname"),
                DB::raw('lead_order_assignments.status as status'),
                'lead_order_assignments.created_at',
                'lead_order_assignments.comment',
                'closest_callbacks.callback_at'
            ])
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', '=', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', '=', 'leads_orders.id')
            ->leftJoin('leads', 'lead_order_assignments.lead_id', '=', 'leads.id')
            ->leftJoin('offers', 'leads.offer_id', '=', 'offers.id')
            ->leftJoin('managers', 'lead_order_routes.manager_id', '=', 'managers.id')
            ->leftJoin('ip_addresses', 'leads.ip', '=', 'ip_addresses.ip')
            ->leftJoinSub(
                $this->callbacks(),
                'closest_callbacks',
                'lead_order_assignments.id',
                '=',
                'closest_callbacks.assignment_id'
            )
            ->orderByDesc('lead_order_assignments.id');
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
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forManager($manager = null)
    {
        if (!empty($manager)) {
            $this->query->whereIn('lead_order_routes.manager_id', Arr::wrap($manager));
        }

        return $this;
    }

    /**
     * Filter results by offer
     *
     * @param int|null $offer
     *
     * @return $this
     */
    public function forOffer($offer = null)
    {
        if (!empty($offer)) {
            $this->query->whereIn('lead_order_routes.offer_id', Arr::wrap($offer));
        }

        return $this;
    }

    /**
     * Filter results by offer
     *
     * @param int|array|null $labels
     *
     * @return $this
     */
    public function forLabels($labels = null)
    {
        if ($labels !== null) {
            $this->query->whereHas('labels', fn ($query) => $query->whereIn('label_id', $labels));
        }

        return $this;
    }

    /**
     * Filter results by status
     *
     * @param null $status
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function havingStatus($status = null)
    {
        if (!empty($status)) {
            $status = Arr::wrap($status);

            $this->query->where(
                fn ($query) => $query->whereIn('lead_order_assignments.status', $status)
                    ->when(in_array('Новый', $status), fn ($query) => $query->orWhere('lead_order_assignments.status', null))
            );
        }

        return $this;
    }

    /**
     * Paginate query results
     *
     * @param int $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 30)
    {
        return $this->query->visible()->paginate($perPage);
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
     * Search, by phone now
     *
     * @param $needle
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function search($needle = null)
    {
        if ($needle) {
            $needle = in_array(mb_substr($needle, 0, 1), [8,7])
            ? substr($needle, 1) :
            $needle;

            $this->query->where(function ($query) use ($needle) {
                return $query->where('phone', 'like', sprintf("%%%s%%", $needle))
                    ->orWhere('lead_order_assignments.alt_phone', 'like', sprintf("%%%s%%", $needle))
                    ->orWhere('firstname', 'ilike', sprintf("%%%s%%", $needle))
                    ->orWhere('lastname', 'ilike', sprintf("%%%s%%", $needle))
                    ->orWhere('middlename', 'ilike', sprintf("%%%s%%", $needle))
                    ->orWhere('lead_order_assignments.comment', 'ilike', sprintf("%%%s%%", $needle))
                    ->orWhere('lead_order_assignments.status', 'ilike', sprintf("%%%s%%", $needle));
            });
        }

        return $this;
    }

    /**
     * Scope query to certain office
     *
     * @param null $office
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forOffice($office = null)
    {
        if (!empty($office)) {
            $this->query->whereIn('leads_orders.office_id', Arr::wrap($office));
        }

        return $this;
    }

    /**
     * Scope query to certain office
     *
     * @param string|null $period
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forPeriod(?string $period)
    {
        if (!empty($period)) {
            $this->query->whereBetween('lead_order_assignments.created_at', $this->parsePeriod($period));
        }

        return $this;
    }


    /**
     * @param string|null $period
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forRegistrationPeriod(?string $period)
    {
        if (!empty($period)) {
            $this->query->whereBetween('lead_order_assignments.registered_at', $this->parsePeriod($period));
        }

        return $this;
    }

    /**
     * @param string $period
     *
     * @return \Illuminate\Support\Carbon[]|null
     */
    public function parsePeriod($period)
    {
        $parts = [];

        preg_match_all('([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))', $period, $parts);

        $until = $parts[0][1] ?? $parts[0][0];

        return [
            Carbon::parse($parts[0][0])->startOfDay(),
            Carbon::parse($until)->endOfDay()
        ];
    }

    /**
     * @param $id
     *
     * @return LeadOrderAssignment|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function find($id)
    {
        return $this->query->where('lead_order_assignments.id', $id)->first();
    }

    /**
     * Filter results by timezone
     *
     * @param string|array|null $timezone
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forTimezone($timezone = null)
    {
        if (!empty($timezone)) {
            $this->query->where(function (Builder $builder) use ($timezone) {
                return $builder->whereIn('lead_order_assignments.timezone', Arr::wrap($timezone))
                    ->orWhereNull('lead_order_assignments.timezone')
                    ->whereIn('ip_addresses.utc_offset', $this->convertMscToUtcOffset($timezone));
            });
        }

        return $this;
    }

    /**
     * Filter results by gender
     *
     * @param string|null $gender
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forGender($gender = null)
    {
        if (!empty($gender)) {
            $this->query->where('lead_order_assignments.gender_id', $gender);
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
            $branches = collect(Arr::wrap($branch));
            $userIds  = User::query()->whereIn('branch_id', $branches->reject(fn ($branch) => $branch === 0))->pluck('id');

            if (in_array(0, $branch)) {
                $this->query->where(function ($query) use ($userIds) {
                    $query->whereNull('leads.user_id')->orWhereIn('leads.user_id', $userIds);
                });
            } else {
                $this->query->whereIn('leads.user_id', $userIds);
            }
        }

        return $this;
    }

    /**
     * @param null $group
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forOfficeGroup($group = null)
    {
        if (!empty($group)) {
            $this->query->whereExists(function (\Illuminate\Database\Query\Builder $query) use ($group) {
                return $query->selectRaw('1')
                    ->from('office_office_group')
                    ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                    ->whereIn('office_office_group.group_id', Arr::wrap($group));
            });
        }

        return $this;
    }

    /**
     * @param null $affiliate
     *
     * @return \App\CRM\Queries\ManagerAssignments
     */
    public function forAffiliate($affiliate = null)
    {
        if (!empty($affiliate)) {
            $this->query->whereIn('leads.affiliate_id', Arr::wrap($affiliate));
        }

        return $this;
    }

    /**
     * @param string|null $smoothLo
     *
     * @return $this
     */
    public function forSmoothLo($smoothLo = null)
    {
        if ($smoothLo === 'without_delayed') {
            $this->query->where(function ($query) {
                $query->whereNull('deliver_at')
                    ->orWhereNotNull('confirmed_at')
                    ->orWhereNotNull('delivery_failed');
            });
        }

        return $this;
    }

    /**
     * Most recent callbacks
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function callbacks()
    {
        return Callback::query()
            ->selectRaw('min(call_at) as callback_at, assignment_id')
            ->whereNull('called_at')
            ->groupBy('assignment_id');
    }

    /**
     * Converts msc offset ('мск+3') to utc ('+0300') for ip_addresses.utc_offset
     *
     * @param string|array|null $timezones
     *
     * @return array
     */
    protected function convertMscToUtcOffset($timezones): array
    {
        $result = [];
        foreach (Arr::wrap($timezones) as $timezone) {
            $utcOffset = (int)preg_replace('~[^0-9\-+]~', '', $timezone) + now('Europe/Moscow')->utcOffset() / 60;

            $result[] = $utcOffset < 0 ? '-' : '+' . Str::padLeft(abs($utcOffset), 2, '0') . '00';
        }

        return $result;
    }
}
