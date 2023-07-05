<?php

namespace App\Reports\Affiliates;

use App\Lead;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\Affiliates
 */
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
     * Offers used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

    /**
     * Affiliates used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $affiliates;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Affiliates\Report
     */
    public static function fromRequest(\Illuminate\Http\Request $request)
    {
        return new self([
            'since'            => $request->get('since'),
            'until'            => $request->get('until'),
            'offers'           => $request->get('offers'),
            'affiliates'       => $request->get('affiliates'),
        ]);
    }

    /**
     * AffiliatesReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forOffers($settings['offers'] ?? null)
            ->forAffiliates($settings['affiliates'] ?? null);
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
        return Lead::visible()->allowedOffers()
            ->select([
                DB::raw('affiliates.name AS affiliate'),
                DB::raw('affiliates.cpl AS cpl'),
                DB::raw('affiliates.cpa AS cpa'),
                DB::raw('offers.name AS offer'),
                DB::raw('count(leads.id) AS leads'),
                DB::raw('count(CASE WHEN leads.valid = TRUE then 1 end) AS validated'),
                DB::raw('count(CASE WHEN leads.valid = false THEN 1 END) AS invalidated'),
                DB::raw('count(deposits.id) AS deposits'),
                DB::raw('count(CASE WHEN leads.valid = TRUE then 1 end) * affiliates.cpl as costleads'),
                DB::raw('COUNT(deposits.id) * affiliates.cpa as costdeposits'),
                DB::raw('COUNT(deposits.id) * 400 as revenue'),
            ])
            ->join('affiliates', 'leads.affiliate_id', 'affiliates.id')
            ->leftJoin('deposits', function (JoinClause $join) {
                return $join->on('leads.id', 'deposits.lead_id')
                    ->whereBetween('lead_return_date', [$this->since, $this->until]);
            })
            ->join('offers', 'leads.offer_id', 'offers.id')
            ->leftJoin('lead_order_assignments', 'leads.id', 'lead_order_assignments.lead_id')
            ->selectRaw(
                "
                count(CASE WHEN lead_order_assignments.status = 'Новый' OR lead_order_assignments.status is null THEN 1 END)
                AS status_new,
                count(CASE WHEN lead_order_assignments.status = 'Отказ' THEN 1 END) AS status_reject,
                count(CASE WHEN lead_order_assignments.status = 'В работе у клоузера' THEN 1 END) AS status_on_closer,
                count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS status_no_answer,
                count(CASE WHEN lead_order_assignments.status = 'Дозвонится' THEN 1 END) AS status_force_call,
                count(CASE WHEN lead_order_assignments.status = 'Демонстрация' THEN 1 END) AS status_demo,
                count(CASE WHEN lead_order_assignments.status = 'Депозит' THEN 1 END) AS status_deposits,
                count(CASE WHEN lead_order_assignments.status = 'Перезвон' THEN 1 END) AS status_callback,
                count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) AS status_double,
                count(CASE WHEN lead_order_assignments.status = 'Резерв' THEN 1 END) AS status_reserve,
                count(CASE WHEN lead_order_assignments.status = 'Неверный номер' THEN 1 END) AS status_wrong_nb"
            )
            ->whereBetween('leads.created_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->groupBy('affiliate', 'cpl', 'cpa', 'offer')
            ->get();
    }

    /**
     * Get a heading row for the report
     *
     * @return array
     */
    protected function headers()
    {
        return Headers::LIST;
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
            $cost = $item->costleads + $item->costdeposits;

            return [
                Fields::AFFILIATE                => $item->affiliate,
                Fields::OFFER                    => $item->offer,
                Fields::LEADS                    => $item->leads,
                Fields::LEADS_VALID              => $item->validated,
                Fields::LEAD_INVALID             => $item->invalidated,
                Fields::FTD                      => $item->deposits,
                Fields::FTD_PERCENT              => sprintf('%s %%', $this->percentage($item->deposits, $item->leads)),
                Fields::REVENUE                  => sprintf('$ %s', $item->revenue),
                Fields::CPL                      => sprintf('$ %s', $item->cpl),
                Fields::CPA                      => sprintf('$ %s', $item->cpa),
                Fields::COST                     => sprintf('$ %s', $cost),
                Fields::PROFIT                   => sprintf('$ %s', $item->revenue - $cost),
                Fields::ROI                      => sprintf('%s %%', $this->percentage($item->revenue - $cost, $cost)),
                Fields::STATUS_NEW               => $item->status_new,
                Fields::STATUS_REJECT            => $item->status_reject,
                Fields::STATUS_ON_CLOSER         => $item->status_on_closer,
                Fields::STATUS_NO_ANSWER         => $item->status_no_answer,
                Fields::STATUS_FORCE_CALL        => $item->status_force_call,
                Fields::STATUS_DEMO              => $item->status_demo,
                Fields::STATUS_DEPOSITS          => $item->status_deposits,
                Fields::STATUS_CALLBACK          => $item->status_callback,
                Fields::STATUS_DOUBLE            => $item->status_double,
                Fields::STATUS_RESERVE           => $item->status_reserve,
                Fields::STATUS_WRING_NB          => $item->status_wrong_nb,
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
        $cost = $data->sum('costleads') + $data->sum('costdeposits');

        return [
            Fields::AFFILIATE                => '',
            Fields::OFFER                    => '',
            Fields::LEADS                    => $data->sum(Fields::LEADS),
            Fields::LEADS_VALID              => $data->sum(Fields::LEADS_VALID),
            Fields::LEAD_INVALID             => $data->sum(Fields::LEAD_INVALID),
            Fields::FTD                      => $data->sum(Fields::FTD),
            Fields::FTD_PERCENT              => sprintf('%s %%', $this->percentage($data->sum(Fields::FTD), $data->sum(Fields::LEADS_VALID))),
            Fields::REVENUE                  => sprintf('$ %s', $data->sum('revenue')),
            Fields::CPL                      => '',
            Fields::CPA                      => '',
            Fields::COST                     => sprintf('$ %s', $cost),
            Fields::PROFIT                   => sprintf('$ %s', $data->sum('revenue') - $cost),
            Fields::ROI                      => sprintf('%s %%', $this->percentage($data->sum('revenue') - $cost, $cost)),
            Fields::STATUS_NEW               => $data->sum(Fields::STATUS_NEW),
            Fields::STATUS_REJECT            => $data->sum(Fields::STATUS_REJECT),
            Fields::STATUS_ON_CLOSER         => $data->sum(Fields::STATUS_ON_CLOSER),
            Fields::STATUS_NO_ANSWER         => $data->sum(Fields::STATUS_NO_ANSWER),
            Fields::STATUS_FORCE_CALL        => $data->sum(Fields::STATUS_FORCE_CALL),
            Fields::STATUS_DEMO              => $data->sum(Fields::STATUS_DEMO),
            Fields::STATUS_DEPOSITS          => $data->sum(Fields::STATUS_DEPOSITS),
            Fields::STATUS_CALLBACK          => $data->sum(Fields::STATUS_CALLBACK),
            Fields::STATUS_DOUBLE            => $data->sum(Fields::STATUS_DOUBLE),
            Fields::STATUS_RESERVE           => $data->sum(Fields::STATUS_RESERVE),
            Fields::STATUS_WRING_NB          => $data->sum(Fields::STATUS_WRING_NB),
        ];
    }

    protected function percentage($one, $two)
    {
        if ($two == 0) {
            return 0;
        }

        return round($one / $two * 100, 2);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\Affiliates\Report
     */
    public function since($since = null)
    {
        $this->since = is_null($since) ? now() : Carbon::parse($since) ?? now();

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\Affiliates\Report
     */
    public function until($until = null)
    {
        $this->until = is_null($until) ? now() : Carbon::parse($until) ?? now();

        return $this;
    }

    /**
     * Filter results for specific offers
     *
     * @param array|null $offers
     *
     * @return \App\Reports\Affiliates\Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Limit report to certain affiliates
     *
     * @param array|null $affiliates
     *
     * @return \App\Reports\Affiliates\Report
     */
    public function forAffiliates($affiliates = null)
    {
        $this->affiliates = $affiliates;

        return $this;
    }
}
