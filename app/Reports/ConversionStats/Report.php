<?php

namespace App\Reports\ConversionStats;

use App\Binom\Statistic;
use App\Deposit;
use App\LeadOrderAssignment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\ConversionStats
 */
class Report implements Responsable, Arrayable
{
    public const LEVEL_MONTH = 'month';
    public const LEVEL_DATE  = 'date';
    public const LEVEL_CREO  = 'creo';

    public const LEVELS = [self::LEVEL_MONTH, self::LEVEL_DATE, self::LEVEL_CREO];
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
     * Offers used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

    /**
     * @var string
     */
    protected string $level;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public static function fromRequest(\Illuminate\Http\Request $request)
    {
        return new self([
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
            'level'       => $request->get('level'),
            'offers'      => $request->get('offers'),
        ]);
    }

    /**
     * DailyReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forLevel($settings['level'] ?? self::LEVEL_MONTH)
            ->forOffers($settings['offers'] ?? null);
    }

    /**
     * Get JSON representation of report
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
     * @inheritDoc
     */
    public function toArray()
    {
        $data = $this->aggregate();

        return [
            'headers' => $this->headers(),
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function aggregate()
    {
        return LeadOrderAssignment::allowedOffers()
            ->selectRaw(
                "
                count(lead_order_assignments.id) as leads_count,
                count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS no_answer,
                count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) AS double,
                COALESCE(binom.leads, 0) as bleads,
                COALESCE(binom.clicks, 0) as bclicks,
                COALESCE(deposits.count, 0) AS ftd
                "
            )
            ->when($this->level === self::LEVEL_MONTH, function (Builder $builder) {
                return $builder->addSelect([
                    DB::raw("date_trunc('month', lead_order_assignments.created_at) as group_name"),
                ])
                    ->leftJoinSub($this->deposits(), 'deposits', function (JoinClause $join) {
                        return $join->on(
                            DB::raw("date_trunc('month', lead_order_assignments.created_at)"),
                            '=',
                            'deposits.date'
                        );
                    })->leftJoinSub($this->binom(), 'binom', function (JoinClause $join) {
                        return $join->on(
                            DB::raw("date_trunc('month', lead_order_assignments.created_at)"),
                            '=',
                            'binom.date'
                        );
                    });
            })
            ->when($this->level === self::LEVEL_DATE, function (Builder $builder) {
                return $builder->addSelect(DB::raw('lead_order_assignments.created_at::date as group_name'))
                    ->leftJoinSub($this->deposits(), 'deposits', function (JoinClause $join) {
                        return $join->on(DB::raw('lead_order_assignments.created_at::date'), '=', 'deposits.date');
                    })
                    ->leftJoinSub($this->binom(), 'binom', function (JoinClause $join) {
                        return $join->on(DB::raw("lead_order_assignments.created_at::date"), '=', 'binom.date');
                    });
            })
            ->when($this->level === self::LEVEL_CREO, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_content as group_name'))
                    ->leftJoin('leads', 'lead_order_assignments.lead_id', 'leads.id')
                    ->leftJoinSub($this->deposits(), 'deposits', function (JoinClause $join) {
                        return $join->on('leads.utm_content', 'deposits.utm_content');
                    })
                    ->leftJoinSub($this->binom(), 'binom', function (JoinClause $join) {
                        return $join->on('leads.utm_content', 'binom.utm_content');
                    });
            })
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->whereBetween('lead_order_assignments.created_at', [$this->since, $this->until])
            ->notEmptyWhereIn('lead_order_routes.offer_id', $this->offers)
            ->groupBy(['group_name', 'bleads', 'bclicks', 'ftd'])
            ->orderBy('group_name')
            ->get();
    }

    /**
     * @return Statistic|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function binom()
    {
        return Statistic::allowedOffers()
            ->select([
                DB::raw('sum(leads) as leads'),
                DB::raw('sum(clicks) as clicks'),
            ])
            ->join('binom_campaigns', 'binom_campaigns.id', 'binom_statistics.campaign_id')
            ->whereBetween('date', [$this->since, $this->until])
            ->notEmptyWhereIn('binom_campaigns.offer_id', $this->offers)
            ->when($this->level === self::LEVEL_MONTH, function (Builder $builder) {
                return $builder->addSelect(DB::raw("date_trunc('month', binom_statistics.date) as date"))
                    ->groupByRaw("date_trunc('month', binom_statistics.date)");
            })
            ->when($this->level === self::LEVEL_DATE, function (Builder $builder) {
                return $builder->addSelect(DB::raw('binom_statistics.date as date'))
                    ->groupBy("binom_statistics.date");
            })
            ->when($this->level === self::LEVEL_CREO, function (Builder $builder) {
                return $builder->addSelect('binom_statistics.utm_content')
                    ->groupBy('binom_statistics.utm_content');
            });
    }

    /**
     * @return Deposit|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function deposits()
    {
        return Deposit::allowedOffers()
            ->select([
                DB::raw('count(*) as count'),
            ])
            ->whereBetween('lead_return_date', [$this->since, $this->until])
            ->when($this->offers, function ($query) {
                return $query->where(function ($q) {
                    return $q->whereIn('deposits.offer_id', $this->offers)
                        ->orWhereNull('deposits.offer_id');
                });
            })
            ->when($this->level === self::LEVEL_MONTH, function (Builder $builder) {
                return $builder->addSelect(DB::raw("date_trunc('month', deposits.lead_return_date) as date"))
                    ->groupByRaw("date_trunc('month', deposits.lead_return_date)");
            })
            ->when($this->level === self::LEVEL_DATE, function (Builder $builder) {
                return $builder->addSelect(DB::raw('deposits.lead_return_date as date'))
                    ->groupBy('deposits.lead_return_date');
            })
            ->when($this->level === self::LEVEL_CREO, function (Builder $builder) {
                return $builder->addSelect('leads.utm_content')
                    ->join('leads', 'deposits.lead_id', 'leads.id')
                    ->groupBy('leads.utm_content');
            });
    }

    /**
     * Get a heading row for the report
     *
     * @return array
     */
    protected function headers()
    {
        return [
            'name',
            'cr',
            'ftd %',
            'leads',
            'no answer',
            'double',
        ];
    }

    /**
     * Gets report's rows
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(function ($item) {
            if ($this->level === self::LEVEL_MONTH) {
                $groupName = Carbon::parse($item->group_name)->format(
                    $this->until->isSameYear($this->since) ? 'F' : 'F (Y)'
                );
            }

            return [
                'group_name'  => $groupName ?? $item->group_name,
                'cr'          => sprintf('%s %%', percentage($item->bleads, $item->bclicks)),
                'ftd_percent' => sprintf('%s %%', percentage($item->ftd, $item->leads_count)),
                'leads_count' => $item->leads_count,
                'no_answer'   => $item->no_answer,
                'double'      => $item->double,
            ];
        });
    }

    /**
     * Get report summary
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary($data)
    {
        return [
            'group_name'  => 'Total',
            'cr'          => sprintf('%s %%', percentage($data->sum('bleads'), $data->sum('bclicks'))),
            'ftd_percent' => sprintf('%s %%', percentage($data->sum('ftd'), $data->sum('leads_count'))),
            'leads_count' => $data->sum('leads_count'),
            'no_answer'   => $data->sum('no_answer'),
            'double'      => $data->sum('double'),
        ];
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
        $this->since = (is_null($since) ? now() : Carbon::parse($since) ?? now())->startOfDay();

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
        $this->until = (is_null($until) ? now() : Carbon::parse($until) ?? now())->endOfDay();

        return $this;
    }

    /**
     * Filter results for specific offers
     *
     * @param array|null $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * @param string $level
     *
     * @return Report
     */
    public function forLevel($level)
    {
        $this->level = in_array($level, self::LEVELS) ? $level : self::LEVEL_MONTH;

        return $this;
    }
}
