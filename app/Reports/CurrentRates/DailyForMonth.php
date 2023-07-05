<?php

namespace App\Reports\CurrentRates;

use App\Deposit;
use App\Insights;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

class DailyForMonth implements Arrayable
{
    public function toArray()
    {
        return [
            'headers' => ['date', 'leads', 'cost', 'cpl', 'ftd', 'ftd%', 'profit', 'roi'],
            'rows'    => [$this->currentMonth(), $this->previousMonth()],
            'summary' => [],
        ];
    }

    public function currentMonth()
    {
        $since  = now()->startOfMonth();
        $until  = now()->subDay();
        $until  = $until->isCurrentMonth() ? $until : now();

        return $this->row($since, $until);
    }

    public function previousMonth()
    {
        $since  = now()->subMonths(1)->startOfMonth();
        $until  = now()->subMonths(1)->endOfMonth();

        return $this->row($since, $until);
    }

    protected function row($since, $until)
    {
        $insights = $this->insights($since, $until);
        $deposits = $this->deposits($since, $until);

        $spend    = $insights->spend ?? 0;
        $leads    = $insights->leads_cnt ?? 0;
        $deposits = $deposits ?? 0;
        $cpl      = $leads > 0 ? $spend / $leads : 0;
        $rev      = $deposits * 400;
        $profit   = $rev - $spend;

        return [
            'date'        => $since->monthName,
            'leads'       => $leads,
            'cost'        => sprintf('$ %s', round($spend, 2)),
            'cpl'         => sprintf('$ %s', round($cpl, 2)),
            'ftd'         => $deposits,
            'ftd_percent' => sprintf('%s %%', percentage($deposits, $leads)),
            'profit'      => sprintf('$ %s', round($profit, 2)),
            'roi'         => sprintf('%s %%', percentage($profit, $spend)),
        ];
    }

    public function insights($since, $until)
    {
        return Insights::allowedOffers()
            ->select([
                DB::raw('sum(spend::DECIMAL) AS spend'),
                DB::raw('sum(leads_cnt) AS leads_cnt'),
            ])
            ->whereBetween('date', [$since, $until])
            ->first();
    }

    public function deposits($since, $until)
    {
        return Deposit::allowedOffers()
            ->select([
                DB::raw('count(*) as count'),
            ])
            ->whereBetween('lead_return_date', [$since, $until])
            ->pluck('count')
            ->first();
    }
}
