<?php

namespace App\Reports\QuickStats;

use App\Insights;
use App\Metrics\Binom\ConversionRate;
use App\Metrics\Binom\Leads;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\Impressions;
use App\Metrics\Facebook\LeadsCount;
use App\Offer;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Report implements Responsable, Arrayable
{

    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * Select report for a specific user
     *
     * @var \App\User|null
     */
    protected $user;

    /**
     * Visible insights for the day
     *
     * @var \Illuminate\Support\Collection
     */
    protected $insights;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forDate($settings['date'] ?? now())
            ->forUser($settings['user'] ?? null);
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
            'date' => Carbon::parse($request->get('date')),
            'user' => $request->get('user'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $user
     *
     * @return \App\Reports\QuickStats\Report
     */
    public function forUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\QuickStats\Report
     */
    public function forDate($date)
    {
        $this->date = $date ?? now();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $this->loadInsights();

        return [
            'headers' => Fields::ALL,
            'rows'    => $this->rows(),
            'summary' => $this->summary()
        ];
    }

    /**
     * Get report rows
     *
     * @return array
     */
    public function rows()
    {
        // Pushing null offer is required, in order to get stats from warming campaigns
        return Offer::all()->push(new Offer(['id' => null, 'name' => 'none']))->map(function (Offer $offer) {
            $insight = $this->insights->where('offer_id', $offer->id)->first();

            if ($insight === null) {
                return [];
            }

            return [
                Fields::OFFER       => $offer->name,
                Fields::IMPRESSIONS => $insight->impressions,
                Fields::CLICKS      => $insight->link_clicks,
                Fields::CPM         => sprintf(
                    '$ %s',
                    round($insight->impressions < 1 ? 0 : $insight->spend / ($insight->impressions / 1000), 2)
                ),
                Fields::CPC         => sprintf(
                    '$ %s',
                    round($insight->link_clicks < 1 ? 0 : $insight->spend / $insight->link_clicks, 2)
                ),
                Fields::CTR         => sprintf(
                    '%s %%',
                    round($insight->impressions < 1 ? 0 : ($insight->link_clicks / $insight->impressions) * 100, 2)
                ),
                Fields::LEADS       => $insight->leads_cnt,
                Fields::BINOM_LEADS => Leads::make()->forUsers($this->user)
                    ->forOffers($offer)
                    ->forDate($this->date)
                    ->metric(),
                Fields::OFFER_CR    => ConversionRate::make()->forOffers($offer)->forDate($this->date)->metric(),
                Fields::CPL         => sprintf(
                    '$ %s',
                    round($insight->leads_cnt < 1 ? 0 : $insight->spend / $insight->leads_cnt, 2)
                ),
                Fields::BINOM_CPL   => \App\Metrics\Binom\CPL::make()
                    ->forUsers($this->user)
                    ->useCosts($insight->spend)
                    ->forDate($this->date)
                    ->forOffers($offer)
                    ->metric(),
                Fields::COST        => sprintf('$ %s', $insight->spend),

            ];
        })->reject(function ($result) {
            return $result === [];
        })->values();
    }

    /**
     * Get report summary
     *
     * @return array
     */
    protected function summary()
    {
        $cost = Cost::make($this->insights);

        return [
            Fields::OFFER       => 'TOTAL',
            Fields::IMPRESSIONS => Impressions::make($this->insights)->metric(),
            Fields::CLICKS      => Clicks::make($this->insights)->metric(),
            Fields::CPM         => CPM::make($this->insights)->metric(),
            Fields::CPC         => CPC::make($this->insights)->metric(),
            Fields::CTR         => CTR::make($this->insights)->metric(),
            Fields::LEADS       => LeadsCount::make($this->insights)->metric(),
            Fields::BINOM_LEADS => Leads::make()->forUsers($this->user)->forDate($this->date)->metric(),
            Fields::OFFER_CR    => ConversionRate::make()->forDate($this->date)->metric(),
            Fields::CPL         => CPL::make($this->insights)->metric(),
            Fields::BINOM_CPL   => \App\Metrics\Binom\CPL::make()->forUsers($this->user)->useCosts($cost->value())
                ->forDate($this->date)->metric(),
            Fields::COST        => $cost->metric(),
        ];
    }

    /**
     * Load insights for all the offers
     *
     * @return \App\Reports\QuickStats\Report
     */
    protected function loadInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->where('date', $this->date)
            ->when($this->user !== null, function ($query) {
                /** @var $query \Illuminate\Database\Eloquent\Builder */
                return $query->whereIn('account_id', User::find($this->user)->accounts()->pluck('account_id')->values());
            })
            ->when($this->user !== null, fn ($query) => $query->where('user_id', $this->user))
            ->groupBy('offer_id')
            ->select([
                'offer_id',
                DB::raw('sum(impressions::decimal) as impressions'),
                DB::raw('sum(link_clicks::decimal) as link_clicks'),
                DB::raw('sum(leads_cnt::decimal) as leads_cnt'),
                DB::raw('sum(spend::decimal) as spend'),
            ])->get();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
