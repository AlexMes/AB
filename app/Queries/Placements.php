<?php

namespace App\Queries;

use App\LeadOrderAssignment;
use App\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class Placements
{
    /**
     * Core table to query
     *
     * @var
     */
    protected $leads;

    /**
     * Placements constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->leads = LeadOrderAssignment::allowedOffers();
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \App\Queries\Placements
     */
    public function forPeriod($since, $until)
    {
        $this->leads->whereBetween(
            'lead_order_assignments.registered_at',
            [
                $since->startOfDay()->toDateTimeString(),
                $until->endOfDay()->toDateTimeString(),
            ]
        );

        return $this;
    }

    /**
     * Filter report by user
     *
     * @param null $users
     *
     * @return $this
     */
    public function forUsers($users = null)
    {
        if (optional(auth()->user())->isVerifier()) {
            $this->leads->where('leads.user_id', auth()->id());

            return $this;
        }

        if ($users === null) {
            return $this;
        }

        $this->leads->whereIn('leads.user_id', $users);

        return $this;
    }

    /**
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Queries\Placements
     */
    public function forAccounts($accounts = null)
    {
        if ($accounts === null) {
            return $this;
        }

        $this->leads->whereIn('leads.account_id', $accounts);

        return $this;
    }

    /**
     * Filter by utm campaign
     *
     * @param null $utmCampaign
     *
     * @return $this
     */
    public function forUtmCampaign($utmCampaign = null)
    {
        if ($utmCampaign === null) {
            return $this;
        }
        $this->leads->where('leads.utm_campaign', $utmCampaign);

        return $this;
    }


    /**
     * Filter by offers
     *
     * @param array $offers
     *
     * @return \App\Queries\Placements
     */
    public function forOffers($offers = null)
    {
        if ($offers === null) {
            $this->leads->whereNotNull('lead_order_routes.offer_id');

            return $this;
        }

        $this->leads->whereIn('lead_order_routes.offer_id', $offers);

        return $this;
    }

    /**
     * @param array|null $placements
     *
     * @return Placements
     */
    public function forPlacements($placements = null)
    {
        if (!empty($placements)) {
            $this->leads->whereIn('utm_medium', $placements);
        }

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param bool $traffic
     *
     * @return \App\Queries\Placements
     */
    public function forTraffic($traffic = null)
    {
        if ($traffic === 'own') {
            $this->leads->whereNull('leads.affiliate_id');

            return $this;
        }
        if ($traffic === 'affiliate') {
            $this->leads->whereNotNull('leads.affiliate_id');

            return $this;
        }

        return $this;
    }

    /**
     * Filter by team
     *
     * @param null|array $teams
     *
     * @return $this
     */
    public function forTeams($teams = null)
    {
        if (!empty($teams)) {
            $this->leads->whereExists(function ($query) use ($teams) {
                return $query->selectRaw('1')
                    ->from('team_user')
                    ->whereColumn('leads.user_id', 'team_user.user_id')
                    ->whereIn('team_user.team_id', $teams);
            });
        }

        return $this;
    }

    /**
     * Perform query and return results
     *
     * @return \App\Lead[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->leads
            ->select([
                'offers.name',
                'leads.utm_medium',
                DB::raw('count(lead_order_assignments.id) as cnt'),
                DB::raw('count(deposits.id) as ftd')])
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->leftJoin('offers', 'lead_order_routes.offer_id', '=', 'offers.id')
            ->groupBy(['offers.name', 'leads.utm_medium'])
            ->orderBy('offers.name')
            ->get();
    }

    /**
     * Named constructor
     *
     * @return \App\Queries\Placements
     */
    public static function fetch()
    {
        return new self();
    }
}
