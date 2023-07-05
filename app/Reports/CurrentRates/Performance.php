<?php

namespace App\Reports\CurrentRates;

use App\Reports\Performance\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

class Performance implements Arrayable
{
    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? null)
            ->until($settings['until'] ?? null);
    }

    /**
     * Array representation for the report
     *
     * @return array
     */
    public function toArray()
    {
        $data = (new \App\Reports\Performance\Report())
            ->since($this->since)
            ->until($this->until)
            ->atLevel(\App\Reports\Performance\Report::LEVEL_CAMPAIGN)
            ->toArray();

        return [
            'headers' => [
                'name', 'offer', 'РК', 'cpm', 'ctr', 'lp ctr', 'offer cr', 'cr', 'leads', 'ftd', 'ftd %', 'cpl',
                'cost', 'profit', 'roi'
            ],
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString()
            ],
        ];
    }

    protected function rows($data)
    {
        return $data['rows']->map(function ($item) {
            return [
                'name'        => $item[Fields::NAME],
                'offer'       => $item[Fields::OFFER],
                'rkCount'     => $item[Fields::RK_COUNT],
                'cpm'         => $item[Fields::CPM],
                'ctr'         => $item[Fields::CTR],
                'crpl'        => $item[Fields::CRPL],
                'crlp'        => $item[Fields::CRLP],
                'CR'          => $item[Fields::CR],
                'leads_cnt'   => $item[Fields::LEADS],
                'ftd_count'   => $item[Fields::FTD],
                'ftd_percent' => $item[Fields::FTD_PERCENT],
                'cpl'         => $item[Fields::CPL],
                'cost'        => $item[Fields::COST],
                'profit'      => $item[Fields::PROFIT],
                'roi'         => $item[Fields::ROI],
            ];
        });
    }

    protected function summary($data)
    {
        return [
            'name'        => $data['summary'][Fields::NAME],
            'offer'       => $data['summary'][Fields::OFFER],
            'rkCount'     => $data['summary'][Fields::RK_COUNT],
            'cpm'         => $data['summary'][Fields::CPM],
            'ctr'         => $data['summary'][Fields::CTR],
            'crpl'        => $data['summary'][Fields::CRPL],
            'crlp'        => $data['summary'][Fields::CRLP],
            'CR'          => $data['summary'][Fields::CR],
            'leads_cnt'   => $data['summary'][Fields::LEADS],
            'ftd_count'   => $data['summary'][Fields::FTD],
            'ftd_percent' => $data['summary'][Fields::FTD_PERCENT],
            'cpl'         => $data['summary'][Fields::CPL],
            'cost'        => $data['summary'][Fields::COST],
            'profit'      => $data['summary'][Fields::PROFIT],
            'roi'         => $data['summary'][Fields::ROI],
        ];
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CurrentRates\Performance
     */
    public function since($date)
    {
        $this->since = is_null($date) ? now()->subDay() : Carbon::parse($date) ?? now()->subDay();

        return $this;
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CurrentRates\Performance
     */
    public function until($date)
    {
        $this->until = is_null($date) ? now()->subDay() : Carbon::parse($date) ?? now()->subDay();

        return $this;
    }
}
