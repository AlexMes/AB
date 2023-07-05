<?php

namespace App\Reports\CurrentRates;

use App\Deposit;
use App\Insights;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Daily implements Arrayable
{
    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forDate($settings['date'] ?? null);
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @return \App\Reports\CurrentRates\Daily
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'date' => $request->get('date'),
        ]);
    }

    public function toArray()
    {
        return [
            'headers' => ['date', 'leads', 'cost', 'cpl', 'ftd', 'ftd%', 'profit', 'roi'],
            'rows'    => [
                $this->row($this->date),
                $this->row($this->date->copy()->subMonth())
            ],
            'summary' => [],
        ];
    }

    protected function row($date)
    {
        $insights = $this->insights($date);
        $deposits = $this->deposits($date);

        $spend    = $insights->spend ?? 0;
        $leads    = $insights->leads_cnt ?? 0;
        $deposits = $deposits ?? 0;
        $cpl      = $leads > 0 ? $spend / $leads : 0;
        $rev      = $deposits * 400;
        $profit   = $rev - $spend;

        return [
            'date'        => $date->toDateString(),
            'leads'       => $leads,
            'cost'        => sprintf('$ %s', round($spend, 2)),
            'cpl'         => sprintf('$ %s', round($cpl, 2)),
            'ftd'         => $deposits,
            'ftd_percent' => sprintf('%s %%', percentage($deposits, $leads)),
            'profit'      => sprintf('$ %s', round($profit, 2)),
            'roi'         => sprintf('%s %%', percentage($profit, $spend)),
        ];
    }

    public function insights($date)
    {
        return Insights::allowedOffers()
            ->select([
                DB::raw('sum(spend::DECIMAL) AS spend'),
                DB::raw('sum(leads_cnt) AS leads_cnt'),
            ])
            ->whereDate('date', $date)
            ->first();
    }

    public function deposits($date)
    {
        return Deposit::allowedOffers()
            ->select([
                DB::raw('count(*) as count'),
            ])
            ->whereDate('lead_return_date', $date)
            ->pluck('count')
            ->first();
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CurrentRates\Daily
     */
    public function forDate($date)
    {
        $this->date = is_null($date) ? now()->subDay() : Carbon::parse($date) ?? now()->subDay();

        return $this;
    }
}
