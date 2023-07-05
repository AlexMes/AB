<?php

namespace App\Reports\OfferStats;

use App\Insights;
use App\LeadOrderAssignment;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Report implements Responsable, Arrayable
{
    /**
     * Select report for a specific date
     *
     * @var \Illuminate\Support\Carbon|null
     */
    protected $since;
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $until;

    /**
     * Select report for a specific offer
     *
     * @var \App\Offer|null
     */
    protected $offer;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forOffer($settings['offer'] ?? null)
            ->forPeriod($settings['since'] ?? null, $settings['until'] ?? null);
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'offer'       => $request->get('offer'),
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $offer
     *
     * @return Report
     */
    public function forOffer($offer = null)
    {
        $this->offer = $offer;

        return  $this;
    }

    /**
     * Filter stats by specific date
     *
     * @param null $since
     * @param null $until
     *
     * @return Report
     */
    public function forPeriod($since = null, $until = null)
    {
        $this->since = $since ? Carbon::parse($since) : now()->startOfMonth();
        $this->until = $until ? Carbon::parse($until) : now();

        return $this;
    }

    /**
     * Get certain offers
     *
     * @return \App\Offer[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return Offer::allowed()
            ->select([
                'offers.name',
                DB::raw('COALESCE(our.leads::integer, 0) + COALESCE(aff.leads::integer,0) as leads'),
                DB::raw('COALESCE(our.ftd::integer, 0) + COALESCE(aff.ftd::integer, 0) as ftd'),
                DB::raw('COALESCE(our.cost::decimal, 0) + COALESCE(aff.cost::decimal, 0) as cost'),
            ])
            ->leftJoinSub($this->ownTraffic(), 'our', fn ($join) => $join->on('offers.id', '=', 'our.offer_id'))
            ->leftJoinSub($this->affiliateTraffic(), 'aff', fn ($join) => $join->on('offers.id', '=', 'aff.offer_id'))
            ->groupBy('offers.id', 'our.leads', 'aff.leads', 'our.ftd', 'aff.ftd', 'our.cost', 'aff.cost')
            ->get();
    }

    /**
     * @return mixed
     */
    protected function ownTraffic()
    {
        return LeadOrderAssignment::allowedOffers()
            ->whereHas('lead', function (Builder $query) {
                return $query->whereNull('affiliate_id')->valid();
            })
            ->select([
                DB::raw('lead_order_routes.offer_id as offer_id'),
                DB::raw('count(lead_order_assignments.id) as leads'),
                DB::raw('count(deposits.id) as ftd'),
                DB::raw('COALESCE(insights.cost, 0) as cost'),
            ])
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->leftJoin('deposits', function (JoinClause $joinClause) {
                return $joinClause->on('lead_order_assignments.lead_id', '=', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id');
            })
            ->leftJoinSub(
                Insights::select(['offer_id', DB::raw('sum(spend::decimal) as cost')])->whereBetween('date', [$this->since, $this->until])->groupBy('offer_id'),
                'insights',
                fn (JoinClause $joinClause) => $joinClause->on('lead_order_routes.offer_id', '=', 'insights.offer_id')
            )
            ->whereBetween('lead_order_assignments.registered_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->groupBy('lead_order_routes.offer_id', 'cost');
    }

    /**
     * Get affiliate stats subquery
     *
     * @return mixed
     */
    protected function affiliateTraffic()
    {
        return LeadOrderAssignment::allowedOffers()
            ->whereNotNull('leads.affiliate_id')
            ->where('leads.valid', true)
            ->select([
                DB::raw('lead_order_routes.offer_id as offer_id'),
                DB::raw('count(lead_order_assignments.id) AS leads'),
                DB::raw('count(deposits.id) as ftd'),
                DB::raw('(count(lead_order_assignments.id) * affiliates.cpl::decimal) + (count(deposits.id) * affiliates.cpa::decimal) as cost'),
            ])
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('affiliates', 'leads.affiliate_id', 'affiliates.id')
            ->leftJoin(
                'deposits',
                fn (JoinClause $join) => $join->on('lead_order_assignments.lead_id', 'deposits.lead_id')
                    ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deposits.lead_return_date')
                    ->whereColumn('leads_orders.office_id', 'deposits.office_id')
            )
            ->whereBetween('lead_order_assignments.registered_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->groupBy('affiliates.cpl', 'affiliates.cpa', 'lead_order_routes.offer_id');
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $data = $this->aggregate();

        return [
            'headers' => Headers::ALL,
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data)
        ];
    }

    /**
     * Get report rows
     *
     * @param mixed $offers
     *
     * @return array
     */
    public function rows($offers)
    {
        return $offers->map(function (Offer $offer) {
            if ($offer->cost > 0) {
                $roi =  ((($offer->ftd * 400) - $offer->cost) / $offer->cost) * 100;
            }

            return [
                Fields::OFFER       => $offer->name,
                Fields::LEADS       => $offer->leads,
                Fields::DEPOSITS    => $offer->ftd,
                Fields::FTD_PERCENT => sprintf("%s %%", percentage($offer->ftd, $offer->leads), 2),
                Fields::CPL         => sprintf(
                    "\$ %s",
                    round($offer->leads ? $offer->cost / $offer->leads : 0, 2)
                ),
                Fields::PROFIT      => sprintf("\$ %s", ($offer->ftd * 400) - $offer->cost),
                Fields::REVENUE     => sprintf("\$ %s", $offer->ftd * 400),
                Fields::COST        => sprintf("\$ %s", round($offer->cost, 2)),
                Fields::ROI         => sprintf("%s %%", round($roi ?? 0, 2)),
            ];
        })->reject(fn ($offer)     => $offer[Fields::LEADS] === 0)
            ->sortByDesc(fn ($row) => $row[Fields::ROI])->values();
    }

    /**
     * Get report summary
     *
     * @param mixed $offers
     *
     * @return array
     */
    public function summary($offers)
    {
        if ($offers->sum('cost')) {
            $roi =  ((($offers->sum('ftd') * 400) - $offers->sum('cost')) / $offers->sum('cost')) * 100;
        }

        return [
            Fields::OFFER       => 'TOTAL',
            Fields::LEADS       => $leads = $offers->sum('leads'),
            Fields::DEPOSITS    => $ftd = $offers->sum('ftd'),
            Fields::FTD_PERCENT => sprintf("%s %%", round(percentage($offers->sum('ftd'), $offers->sum('leads')), 2)),
            Fields::CPL         => sprintf(
                "\$ %s",
                round(($offers->sum('leads') > 0) ? ($offers->sum('cost') / $offers->sum('leads')) : 0, 2)
            ),
            Fields::PROFIT      => sprintf("\$ %s", round(($offers->sum('ftd') * 400) - $offers->sum('cost'), 2)),
            Fields::REVENUE     => sprintf("\$ %s", $offers->sum('ftd') * 400),
            Fields::COST        => sprintf("\$ %s", round($offers->sum('cost'), 2)),
            Fields::ROI         => sprintf("%s %%", round($roi ?? 0, 2)),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
