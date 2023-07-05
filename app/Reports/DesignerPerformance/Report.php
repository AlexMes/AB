<?php

namespace App\Reports\DesignerPerformance;

use App\Binom\Statistic;
use App\Deposit;
use App\Insights;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
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
     * Determines when we have to cut FB columns
     *
     * @var bool
     */
    protected bool $hideFacebook;

    /**
     * @var Insights[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $insights;

    /**
     * @var Statistic[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $binom;

    /**
     * @var Deposit[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    protected $deposits;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\DesignerPerformance\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'hideFacebook' => $request->boolean('hideFacebook'),
        ]);
    }

    /**
     * DesignerPefromance constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->hideFacebook();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\DesignerPerformance\Report
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
     * @return \App\Reports\DesignerPerformance\Report
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
     * Set visibility for Facebook data
     *
     * @return \App\Reports\DesignerPerformance\Report
     */
    protected function hideFacebook()
    {
        $this->hideFacebook = ! auth()->user()->showFbFields;

        return  $this;
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
        $this->insights();
        $this->binom();
        $this->deposits();

        return [
            'headers'  => $this->hideFacebook ? array_diff(Headers::ALL, Headers::fbKeys()) : Headers::ALL,
            'rows'     => $this->rows(),
            'summary'  => $this->summary(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * Gets insights
     */
    protected function insights()
    {
        $this->insights = Insights::allowedOffers()
            ->select([
                DB::raw('designers.name as designer'),
                DB::raw('sum(leads_cnt) as leads'),
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(link_clicks::int) as clicks'),
                DB::raw('sum(spend::DECIMAL) AS spend'),
            ])
            ->join('facebook_ads', 'facebook_cached_insights.ad_id', '=', 'facebook_ads.id')
            ->join('designers', 'facebook_ads.designer_id', '=', 'designers.id')
            ->whereBetween('facebook_cached_insights.date', [$this->since, $this->until])
            ->groupBy('designer')
            ->get();
    }

    /**
     * Gets binom statistics
     */
    protected function binom()
    {
        $this->binom = Statistic::allowedOffers()
            ->select([
                DB::raw('designers.name as designer'),
                DB::raw('sum(binom_statistics.clicks) as bclicks'),
                DB::raw('sum(binom_statistics.lp_views) as lp_views'),
                DB::raw('sum(binom_statistics.lp_clicks) as lp_clicks'),
                DB::raw('sum(binom_statistics.leads) AS bleads'),
            ])
            ->join('facebook_ads', 'binom_statistics.fb_ad_id', '=', 'facebook_ads.id')
            ->join('designers', 'facebook_ads.designer_id', '=', 'designers.id')
            ->whereBetween('binom_statistics.date', [$this->since, $this->until])
            ->groupBy('designer')
            ->get()
            ->keyBy('designer');
    }

    /**
     * Gets deposits
     */
    protected function deposits()
    {
        $this->deposits = Deposit::allowedOffers()
            ->select([
                DB::raw('designers.name as designer'),
                DB::raw('count(DISTINCT deposits.id) AS ftd'),
            ])
            ->join('leads', 'deposits.lead_id', '=', 'leads.id')
            ->join('facebook_ads', 'leads.utm_content', '=', 'facebook_ads.name')
            ->join('designers', 'facebook_ads.designer_id', '=', 'designers.id')
            ->whereBetween('deposits.lead_return_date', [$this->since, $this->until])
            ->groupBy('designer')
            ->get()
            ->keyBy('designer');
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function rows()
    {
        return $this->insights->map(function ($row) {
            $binom = $this->binom->get($row->designer);
            $deposits = $this->deposits->get($row->designer);

            $result = [
                Fields::NAME          => $row->designer,
                Fields::IMPRESSIONS   => $row->impressions,
                Fields::CLICKS        => $row->clicks ?: 0,
                Fields::CPM           => $row->impressions != 0
                    ? round($row->spend / ($row->impressions / 1000), 2)
                    : 0,
                Fields::CPC           => $row->clicks != 0 ? round($row->spend / $row->clicks, 2) : 0,
                Fields::CTR           => $row->impressions != 0 ? round($row->clicks / $row->impressions * 100, 2) : 0,

                Fields::BCLICKS       => $binom->bclicks ?: 0,
                Fields::LPVIEWS       => $binom->lp_views ?: 0,
                Fields::LPCLICKS      => $binom->lp_clicks ?: 0,
                Fields::CRPL          => $binom->lp_views != 0
                    ? round($binom->lp_clicks / $binom->lp_views * 100, 2)
                    : 0,
                Fields::CRLP          => $binom->lp_clicks != 0
                    ? round($binom->bleads / $binom->lp_clicks * 100, 2)
                    : 0,
                Fields::CR            => $binom->bclicks != 0 ? round($binom->bleads / $binom->bclicks * 100, 2) : 0,
                Fields::LEADS         => $row->leads ?: 0,
                Fields::BLEADS        => $binom->bleads ?: 0,

                Fields::FTD           => optional($deposits)->ftd ?? 0,
                Fields::FTD_PERCENT   => $row->leads != 0 ? round(optional($deposits)->ftd / $row->leads * 100, 2) : 0,
                Fields::FTD_COST      => optional($deposits)->ftd != 0 ? round($row->spend / optional($deposits)->ftd, 2) : 0,
                Fields::BFTD_PERCENT  => $binom->bleads != 0 ? round(optional($deposits)->ftd / $binom->bleads * 100, 2) : 0,
                Fields::REVENUE       => round($revenue = optional($deposits)->ftd * 400, 2),
                Fields::CPL           => $row->leads != 0 ? round($row->spend / $row->leads, 2) : 0,
                Fields::BCPL          => $binom->bleads != 0 ? round($row->spend / $binom->bleads, 2) : 0,
                Fields::COST          => $row->spend ?: 0,
                Fields::PROFIT        => round($profit = $revenue - $row->spend, 2),
                Fields::ROI           => $row->spend != 0 ? round($profit / $row->spend * 100, 2) : 0,
            ];

            return $this->hideFacebook ? Arr::except($result, Fields::fbKeys()) : $result;
        });
    }

    /**
     * @return array
     */
    protected function summary()
    {
        $result = [
            Fields::NAME          => 'Итого',
            Fields::IMPRESSIONS   => $this->insights->sum('impressions'),
            Fields::CLICKS        => $this->insights->sum('clicks') ?: 0,
            Fields::CPM           => $this->insights->sum('impressions') != 0
                ? round($this->insights->sum('spend') / ($this->insights->sum('impressions') / 1000), 2)
                : 0,
            Fields::CPC           => $this->insights->sum('clicks') != 0
                ? round($this->insights->sum('spend') / $this->insights->sum('clicks'), 2)
                : 0,
            Fields::CTR           => $this->insights->sum('impressions') != 0
                ? round($this->insights->sum('clicks') / $this->insights->sum('impressions') * 100, 2)
                : 0,

            Fields::BCLICKS       => $this->binom->sum('bclicks') ?: 0,
            Fields::LPVIEWS       => $this->binom->sum('lp_views') ?: 0,
            Fields::LPCLICKS      => $this->binom->sum('lp_clicks') ?: 0,
            Fields::CRPL          => $this->binom->sum('lp_views') != 0
                ? round($this->binom->sum('lp_clicks') / $this->binom->sum('lp_views') * 100, 2)
                : 0,
            Fields::CRLP      => $this->binom->sum('lp_clicks') != 0
                ? round($this->binom->sum('bleads') / $this->binom->sum('lp_clicks') * 100, 2)
                : 0,
            Fields::CR            => $this->binom->sum('bclicks') != 0
                ? round($this->binom->sum('bleads') / $this->binom->sum('bclicks') * 100, 2)
                : 0,
            Fields::LEADS         => $this->insights->sum('leads') ?: 0,
            Fields::BLEADS        => $this->binom->sum('bleads') ?: 0,

            Fields::FTD           => $this->deposits->sum('ftd') ?: 0,
            Fields::FTD_PERCENT   => $this->insights->sum('leads') != 0
                ? round($this->deposits->sum('ftd') / $this->insights->sum('leads') * 100, 2)
                : 0,
            Fields::FTD_COST      => $this->deposits->sum('ftd') != 0
                ? round($this->insights->sum('spend') / $this->deposits->sum('ftd'), 2)
                : 0,
            Fields::BFTD_PERCENT  => $this->binom->sum('bleads') != 0
                ? round($this->deposits->sum('ftd') / $this->binom->sum('bleads') * 100, 2)
                : 0,
            Fields::REVENUE       => round($revenue = $this->deposits->sum('ftd') * 400, 2),
            Fields::CPL           => $this->insights->sum('leads') != 0
                ? round($this->insights->sum('spend') / $this->insights->sum('leads'), 2)
                : 0,
            Fields::BCPL          => $this->binom->sum('bleads') != 0
                ? round($this->insights->sum('spend') / $this->binom->sum('bleads'), 2)
                : 0,
            Fields::COST          => round($this->insights->sum('spend'), 2) ?: 0,
            Fields::PROFIT        => round($profit = $revenue - $this->insights->sum('spend'), 2),
            Fields::ROI           => $this->insights->sum('spend') != 0
                ? round($profit / $this->insights->sum('spend') * 100, 2)
                : 0,
        ];

        return $this->hideFacebook ? Arr::except($result, Fields::fbKeys()) : $result;
    }
}
