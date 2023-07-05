<?php

namespace App\Reports\OfficesStats;

use App\Insights;
use App\Metrics\Binom\CPL;
use App\Office;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
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
     * Select report for a specific office
     *
     * @var \App\Office[]|null
     */
    protected $offices;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forOffice($settings['office'] ?? null)
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
            'office'      => $request->get('office'),
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $offices
     *
     * @return Report
     */
    public function forOffice($offices = null)
    {
        $this->offices = $offices;

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
     * Get certain offices
     *
     * @return \App\Office[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function getOffices()
    {
        return Office::query()
            ->notEmptyWhereIn('id', $this->offices)
            ->withCount([
                'deposits' => function ($query) {
                    return $query->visible()->whereBetween('lead_return_date', [
                        $this->since->toDateString(),
                        $this->until->toDateString()
                    ])->allowedOffers();
                },
                'results' => function ($query) {
                    return $query->visible()
                        ->select(DB::raw('sum(leads_count)'))
                        ->whereBetween('date', [
                            $this->since->toDateString(),
                            $this->until->toDateString()
                        ])
                        ->allowedOffers();
                }
            ])
            ->get();
    }


    /**
     * Get insights
     *
     * @return float
     */
    public function getCost()
    {
        return Insights::allowedOffers()
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->sum(DB::raw('spend::decimal'));
    }

    /**
     * Get Binom CPL
     *
     * @return string
     */
    protected function getBcpl()
    {
        return \App\Metrics\Binom\CPL::make()
            ->since($this->since)
            ->until($this->until)
            ->useCosts($this->getCost())
            ->value();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $data = $this->getOffices();
        $cpl  = $this->getBcpl();

        return [
            'headers' => Headers::ALL,
            'rows'    => $this->rows($data, $cpl),
            'summary' => $this->summary($data, $cpl)
        ];
    }

    /**
     * Get report rows
     *
     * @param mixed $offices
     * @param mixed $cpl
     *
     * @return array
     */
    public function rows($offices, $cpl)
    {
        return $offices->map(function (Office $office) use ($cpl) {
            $cost = $cpl * (int)$office->results_count;
            $roi = 0;
            if ($cost) {
                $roi = ((($office->deposits_count * 400) - $cost) / $cost) * 100;
            }

            return [
                Fields::OFFICE      => $office->name,
                Fields::LEADS       => $leads = (int) $office->results_count,
                Fields::DEPOSITS    => $ftd = (int) $office->deposits_count,
                Fields::FTD_PERCENT => sprintf("%s %%", round($leads > 0 ? ($ftd / $leads) * 100 : 0, 2)),
                //                Fields::CPL         => sprintf("\$ %s", round($cpl, 2)),
                //                Fields::PROFIT      => sprintf("\$ %d", ($office->deposits_count * 400) - $cost),
                //                Fields::REVENUE     => sprintf("\$ %d", $office->deposits_count * 400),
                //                Fields::COST        => sprintf("\$ %d", round($cost, 2)),
                //                Fields::ROI         => sprintf("%d %%", $roi),
            ];
        })->reject(fn ($office) => $office[Fields::LEADS] === 0)->values();
    }

    /**
     * Get report summary
     *
     * @param mixed $offices
     * @param mixed $cpl
     *
     * @return array
     */
    public function summary($offices, $cpl)
    {
        $cost = $cpl * $offices->sum('results_count');
        $roi  = 0;
        if ($cost) {
            $roi = ((($offices->sum('deposits_count') * 400) - $cost) / $cost) * 100;
        }

        return [
            Fields::OFFICE      => 'TOTAL',
            Fields::LEADS       => $leads = $offices->sum('results_count'),
            Fields::DEPOSITS    => $ftd = $offices->sum('deposits_count'),
            Fields::FTD_PERCENT => sprintf("%s %%", round($leads > 0 ? ($ftd / $leads) * 100 : 0, 2)),
            //            Fields::CPL         => sprintf("\$ %s", round($cpl, 2)),
            //            Fields::PROFIT      => sprintf("\$ %d", ($offices->sum('deposits_count') * 400) - $cost),
            //            Fields::REVENUE     => sprintf("\$ %d", $offices->sum('deposits_count') * 400),
            //            Fields::COST        => sprintf("\$ %d", round($cost, 2)),
            //            Fields::ROI         => sprintf("%d %%", $roi),
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
