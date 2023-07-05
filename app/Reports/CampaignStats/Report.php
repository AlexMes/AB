<?php

namespace App\Reports\CampaignStats;

use App\Facebook\Campaign;
use App\Insights;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\Cost;
use App\Metrics\Facebook\CPC;
use App\Metrics\Facebook\CPL;
use App\Metrics\Facebook\CPM;
use App\Metrics\Facebook\CTR;
use App\Metrics\Facebook\Impressions;
use App\Metrics\Facebook\LeadsCount;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
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
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Key to lookup campaigns
     *
     * @var string
     */
    protected $campaign;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forDate($settings['date'] ?? now())
            ->forUser($settings['user'] ?? null)
            ->forCampaign($settings['campaign'] ?? null);
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
            'date'      => Carbon::parse($request->get('date')),
            'user'      => $request->get('user'),
            'campaign'  => $request->get('campaign'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $user
     *
     * @return \App\Reports\CampaignStats\Report
     */
    public function forUser($user)
    {
        $this->user = User::find($user);

        return  $this;
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CampaignStats\Report
     */
    public function forDate($date)
    {
        $this->date = $date ?? now();

        return $this;
    }

    /**
     * Key for lookup
     *
     * @param string $key
     *
     * @return \App\Reports\CampaignStats\Report
     */
    protected function forCampaign($key)
    {
        $this->campaign = $key;

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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function rows()
    {
        return $this->insights->map(function ($insight) {
            return [
                Fields::CAMPAIGN    => $insight->name,
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
                Fields::CPL         => sprintf(
                    '$ %s',
                    round($insight->leads_cnt < 1 ? 0 : $insight->spend / $insight->leads_cnt, 2)
                ),
                Fields::COST        => sprintf('$ %s', $insight->spend),
            ];
        })->reject(function ($result) {
            return $result[Fields::IMPRESSIONS] === 0;
        })->values();
    }

    /**
     * Get report summary
     *
     * @return array
     */
    public function summary(): array
    {
        return [
            Fields::CAMPAIGN    => 'TOTAL',
            Fields::IMPRESSIONS => Impressions::make($this->insights)->metric(),
            Fields::CLICKS      => Clicks::make($this->insights)->metric(),
            Fields::CPM         => CPM::make($this->insights)->metric(),
            Fields::CPC         => CPC::make($this->insights)->metric(),
            Fields::CTR         => CTR::make($this->insights)->metric(),
            Fields::LEADS       => LeadsCount::make($this->insights)->metric(),
            Fields::CPL         => CPL::make($this->insights)->metric(),
            Fields::COST        => Cost::make($this->insights)->metric(),
        ];
    }

    /**
     * Load insights for all the offers
     *
     * @return \App\Reports\CampaignStats\Report
     */
    protected function loadInsights()
    {
        $this->insights = Insights::visible()
            ->allowedOffers()
            ->where('date', $this->date)
            ->whereIn('campaign_id', Campaign::visible()->when($this->campaign, function (Builder $query) {
                $query->where('name', 'like', "%{$this->campaign}");
            })->pluck('id'))
            ->when($this->user !== null, function ($query) {
                /** @var $query \Illuminate\Database\Eloquent\Builder */
                return $query->whereIn('account_id', $this->user->accounts()->pluck('account_id')->values());
            })
            ->when($this->user !== null, fn ($query) => $query->where('user_id', $this->user->id))
            ->groupBy('campaign_id')
            ->select([
                'campaign_id',
                'name' => Campaign::select('name')
                    ->whereColumn('id', 'facebook_cached_insights.campaign_id')
                    ->limit(1),
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
