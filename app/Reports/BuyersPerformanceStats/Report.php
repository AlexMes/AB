<?php

namespace App\Reports\BuyersPerformanceStats;

use App\Binom\Statistic;
use App\Deposit;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Report implements Responsable, Arrayable
{
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $since;

    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $until;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now());
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
            'since'     => $request->get('since'),
            'until'     => $request->get('until'),
        ]);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\BuyersPerformanceStats\Report
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
     * @return \App\Reports\BuyersPerformanceStats\Report
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
     * {@inheritDoc}
     */
    public function toArray()
    {
        $data = $this->aggregate();

        return [
            'headers' => ['name','accounts','cpm','ctr','leads','ftd','ftd %','ftd cost','cpl','cost','profit','roi'],
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
        ];
    }

    protected function aggregate()
    {
        return DB::table('facebook_cached_insights')
            ->select([
                DB::raw('users.name as buyer'),
                DB::raw('count(DISTINCT facebook_cached_insights.account_id) AS accounts'),
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(link_clicks::int) as clicks'),
                DB::raw('sum(spend::DECIMAL) / nullif(sum(impressions) / 1000, 0) AS cpm'),
                DB::raw('sum(link_clicks::int) / nullif(sum(impressions), 0) * 100 AS ctr'),
                DB::raw('binom.leads AS leads'),
                DB::raw('COALESCE(deposits.count, 0) AS ftd'),
                DB::raw('sum(spend::DECIMAL) / nullif(binom.leads, 0) AS cpl'),
                DB::raw('sum(spend::DECIMAL) AS cost'),
            ])
            ->join('facebook_ads_accounts', 'facebook_cached_insights.account_id', '=', 'facebook_ads_accounts.account_id')
            ->join('facebook_profiles', 'facebook_ads_accounts.profile_id', '=', 'facebook_profiles.id')
            ->join('users', 'facebook_profiles.user_id', '=', 'users.id')
            ->leftJoinSub($this->binom(), 'binom', fn ($join) => $join->on('users.id', '=', 'binom.user_id'))
            ->leftJoinSub($this->deposits(), 'deposits', fn ($join) => $join->on('users.id', '=', 'deposits.user_id'))
            ->whereBetween('date', [$this->since, $this->until])
            ->whereNotNull('offer_id')
            ->unless(auth()->user()->isAdmin(), function (Builder $query) {
                return $query->whereIn('facebook_cached_insights.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
            })
            ->groupBy(['buyer','ftd','binom.leads'])
            ->get();
    }

    /**
     * @internal
     */
    protected function binom()
    {
        return Statistic::allowedOffers()
            ->select([
                'user_id',
                DB::raw('sum(leads) as leads')
            ])->whereBetween('date', [$this->since, $this->until])->groupBy('user_id');
    }

    /**
     * @internal
     */
    protected function deposits()
    {
        return Deposit::allowedOffers()
            ->select([
                'user_id',
                DB::raw('count(deposits.id) as count')
            ])->whereBetween('lead_return_date', [$this->since, $this->until])->groupBy('user_id');
    }

    protected function rows(Collection $data)
    {
        return $data->map(fn ($buyer) => [
            'name'        => $buyer->buyer,
            'accounts'    => $buyer->accounts,
            'cpm'         => round($buyer->cpm, 2),
            'ctr'         => round($buyer->ctr, 2),
            'leads'       => $buyer->leads ?? 0,
            'ftd'         => $buyer->ftd,
            'cr'          => $buyer->leads == 0 ? 0 : round(($buyer->ftd / $buyer->leads) * 100, 2),
            'ftdcost'     => $buyer->ftd == 0 ? 0 : round($buyer->cost / $buyer->ftd, 2),
            'cpl'         => round($buyer->cpl, 2) ?? 0,
            'cost'        => $buyer->cost,
            'profit'      => round(($buyer->ftd * 400 - $buyer->cost), 2),
            'roi'         => round(
                (($buyer->ftd * 400 - $buyer->cost) / $buyer->cost) * 100,
                2
            ),
        ]);
    }

    protected function summary($data)
    {
        return [
            'name'        => 'TOTAL',
            'accounts'    => $data->sum('accounts'),
            'cpm'         => $data->sum('impressions') == 0 ? 0 : round($data->sum('cost') / ($data->sum('impressions') / 1000), 2),
            'ctr'         => $data->sum('impressions') == 0 ? 0 : round(($data->sum('clicks') / $data->sum('impressions')) * 100, 2),
            'leads'       => $data->sum('leads'),
            'ftd'         => $data->sum('ftd'),
            'cr'          => $data->sum('leads') == 0 ? 0 : round(($data->sum('ftd') / $data->sum('leads')) * 100, 2),
            'ftdcost'     => $data->sum('ftd') == 0 ? 0 : round($data->sum('cost') / $data->sum('ftd'), 2),
            'cpl'         => round($data->sum('leads') ? $data->sum('cost') / $data->sum('leads') : 0, 2),
            'cost'        => round($data->sum('cost'), 2),
            'profit'      => round(($data->sum('ftd') * 400 - $data->sum('cost')), 2),
            'roi'         => round(
                $data->sum('cost') ? (($data->sum('ftd') * 400 - $data->sum('cost')) / $data->sum('cost')) * 100 : 0,
                2
            ),
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
