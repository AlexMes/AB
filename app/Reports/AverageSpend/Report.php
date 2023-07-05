<?php

namespace App\Reports\AverageSpend;

use App\Insights;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Report implements Arrayable, Responsable
{
    /**
     * @var Carbon
     */
    protected $since;

    /**
     * @var Carbon
     */
    protected $until;

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
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
        ]);
    }

    /**
     * GenderReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $rows = $this->collectData();

        return [
            'headers' => ['creo', 'count', 'spend(total)', 'spend(avg)'],
            'rows'    => $this->rows($rows),
            'summary' => $this->summary($rows),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    protected function rows(Collection $rows)
    {
        return $rows->map(function ($row) {
            return [
                'utm_campaign'  => $row->name,
                'ad_cnt'        => $row->ad_cnt,
                'spend'         => sprintf('$ %s', $row->spend),
                'spend_avg'     => sprintf('$ %s', $row->ad_cnt == 0 ? 0 : round($row->spend / $row->ad_cnt, 2)),
            ];
        });
    }

    public function summary(Collection $rows)
    {
        return [
            'utm_campaign'  => 'ИТОГО',
            'ad_cnt'        => $adCount = $rows->sum('ad_cnt'),
            'spend'         => sprintf('$ %s', ($spend = $rows->sum('spend'))),
            'spend_avg'     => sprintf('$ %s', $adCount == 0 ? 0 : round($spend / $adCount, 2)),
        ];
    }

    /**
     * Collect report data
     *
     * @return \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection
     */
    protected function collectData()
    {
        return Insights::allowedOffers()
            ->selectRaw('
                facebook_ads.name as name,
                sum(facebook_cached_insights.spend::numeric) as spend,
                count(facebook_ads.name) as ad_cnt
            ')
            ->whereBetween('date', [$this->since->toDateTimeString(), $this->until->toDateTimeString()])
            ->whereNotNull('offer_id')
            ->rightJoin('facebook_ads', 'facebook_ads.id', 'facebook_cached_insights.ad_id')
            ->groupBy('name')
            ->orderBy('name')
            ->get();
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
}
