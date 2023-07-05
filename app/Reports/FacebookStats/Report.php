<?php

namespace App\Reports\FacebookStats;

use App\Insights;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
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
     * @return $this
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
     * @return $this
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
            'headers' => ['account', 'clicks', 'cost', 'cpm', 'ctr', 'cpc', 'leads', 'cpl'],
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
        ];
    }

    protected function aggregate()
    {
        return Insights::query()
            ->visible()
            ->select([
                'facebook_cached_insights.account_id',
                DB::raw('sum(link_clicks::decimal) as clicks'),
                DB::raw('sum(leads_cnt) as leads_cnt'),
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(spend::decimal) as cost'),
                DB::raw('sum(link_clicks::decimal) / sum(impressions) * 100 as ctr'),
                DB::raw('sum(spend::decimal) / (sum(impressions) / 1000) as cpm'),
                DB::raw('sum(spend::decimal) / sum(link_clicks::decimal) as cpc'),
                DB::raw('sum(spend::decimal) / sum(leads_cnt) as cpl'),
            ])
            ->whereBetween('date', [$this->since, $this->until])
            ->groupBy('facebook_cached_insights.account_id')
            ->with('account')
            ->get();
    }

    protected function rows(Collection $data)
    {
        return $data->map(fn ($item) => [
            'account'     => optional($item->account)->name,
            'clicks'      => $item->clicks ?? 0,
            'cost'        => round($item->cost ?? 0, 2),
            'cpm'         => round($item->cpm ?? 0, 2),
            'ctr'         => round($item->ctr ?? 0, 2),
            'cpc'         => round($item->cpc ?? 0, 2),
            'leads'       => $item->leads_cnt ?? 0,
            'cpl'         => round($item->cpl ?? 0, 2),
        ]);
    }

    protected function summary(Collection $data)
    {
        return [
            'account'     => 'ИТОГО',
            'clicks'      => $data->sum('clicks'),
            'cost'        => round($data->sum('cost'), 2),
            'cpm'         => $data->sum('impressions') > 0
                ? round($data->sum('cost') / ($data->sum('impressions') / 1000), 2)
                : 0,
            'ctr'         => $data->sum('impressions') > 0
                ? round($data->sum('clicks') / $data->sum('impressions') * 100, 2)
                : 0,
            'cpc'         => $data->sum('clicks') > 0
                ? round($data->sum('cost') / $data->sum('clicks'), 2)
                : 0,
            'leads'       => $data->sum('leads_cnt'),
            'cpl'         => $data->sum('leads_cnt') > 0
                ? round($data->sum('cost') / $data->sum('leads_cnt'), 2)
                : 0,
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
