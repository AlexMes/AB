<?php

namespace App\Reports\DailyOpt;

use App\Binom\Statistic;
use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use App\Offer;
use App\Office;
use App\Result;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\DailyOpt
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
     * Offices used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offices;

    /**
     * Offers used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $offers;

    /**
     * Limit results to some users
     *
     * @var array|null
     */
    protected $users;

    /**
     * @var array|null
     */
    protected $officeGroups;

    protected $allOffices;

    protected $allOffers;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\DailyOpt\Report
     */
    public static function fromRequest(\Illuminate\Http\Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'offices'       => $request->get('offices'),
            'offers'        => $request->get('offers'),
            'users'         => $request->get('users'),
            'office_groups' => $request->get('office_groups'),
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
            ->forOffers($settings['offers'] ?? null)
            ->forOffices($settings['offices'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forOfficeGroups($settings['office_groups'] ?? null);

        $this->allOffices = Office::all(['id', 'name'])->keyBy('id');
        $this->allOffers  = Offer::all('id', 'name')->keyBy('id');
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
            /*'returned'  => optional($late)->returnedMetric() ?? [],
            'late'      => optional($late)->lateMetric() ?? [],*/
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function aggregate()
    {
        return Result::allowedOffers()
            ->visible()
            ->select([
                'results.date',
                'results.office_id',
                'results.offer_id',
                'leads_count',
                DB::raw('COALESCE(binom.lp_clicks) as lp_clicks'),
                DB::raw('COALESCE(binom.lp_views) as lp_views'),
                DB::raw('COALESCE(binom.leads) as bleads'),
                DB::raw('COALESCE(binom.clicks) as bclicks'),
                DB::raw('COALESCE(insights.impressions) as impressions'),
                DB::raw('COALESCE(insights.link_clicks) as link_clicks'),
                DB::raw('COALESCE(insights.spend) as spend'),
                DB::raw('COALESCE(insights.leads_cnt) as leads_cnt'),
                DB::raw('COALESCE(deposits.count, 0) AS ftd'),
            ])
            ->leftJoinSub($this->binom(), 'binom', function (JoinClause $join) {
                return $join->on('results.date', '=', 'binom.date')
                    ->on('results.offer_id', '=', 'binom.offer_id');
            })
            ->leftJoinSub($this->deposits(), 'deposits', function (JoinClause $join) {
                return $join->on('results.date', '=', 'deposits.date')
                    ->on('results.office_id', '=', 'deposits.office_id')
                    ->on('results.offer_id', '=', 'deposits.offer_id');
            })
            ->leftJoinSub($this->insights(), 'insights', function (JoinClause $join) {
                return $join->on('results.date', '=', 'insights.date')
                    ->on('results.offer_id', '=', 'insights.offer_id');
            })
            ->whereBetween('results.date', [$this->since, $this->until])
            ->notEmptyWhereIn('results.offer_id', $this->offers)
            ->when($this->offices, function ($query) {
                return $query->where(function ($q) {
                    return $q->whereIn('results.office_id', $this->offices)
                        ->orWhereNull('results.office_id');
                });
            })
            ->when($this->officeGroups, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('results.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->orderByDesc('date')
            ->get();
    }

    /**
     * @return Statistic|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function binom()
    {
        return Statistic::allowedOffers()
            ->select([
                'binom_statistics.date as date',
                'binom_campaigns.offer_id',
                DB::raw('sum(lp_clicks) as lp_clicks'),
                DB::raw('sum(lp_views) as lp_views'),
                DB::raw('sum(leads) as leads'),
                DB::raw('sum(clicks) as clicks'),
            ])
            ->join('binom_campaigns', 'binom_campaigns.id', 'binom_statistics.campaign_id')
            ->whereBetween('date', [$this->since, $this->until])
            ->notEmptyWhereIn('binom_campaigns.offer_id', $this->offers)
            ->notEmptyWhereIn('user_id', $this->users)
            ->groupBy('date', 'binom_campaigns.offer_id');
    }

    /**
     * @return Deposit|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function deposits()
    {
        return Deposit::allowedOffers()
            ->select([
                DB::raw('lead_return_date as date'),
                'offer_id',
                'office_id',
                DB::raw('count(*) as count'),
            ])
            ->whereBetween('lead_return_date', [$this->since, $this->until])
            ->when($this->offers, function ($query) {
                return $query->where(function ($q) {
                    return $q->whereIn('offer_id', $this->offers)
                        ->orWhereNull('offer_id');
                });
            })
            ->when($this->offices, function ($query) {
                return $query->where(function ($q) {
                    return $q->whereIn('office_id', $this->offices)
                        ->orWhereNull('office_id');
                });
            })
            ->when($this->officeGroups, function ($query) {
                return $query->whereExists(function ($q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->notEmptyWhereIn('user_id', $this->users)
            ->groupBy('lead_return_date', 'office_id', 'offer_id');
    }

    /**
     * @return Insights|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function insights()
    {
        return Insights::allowedOffers()
            ->select([
                'date',
                'offer_id',
                DB::raw('sum(impressions) as impressions'),
                DB::raw('sum(link_clicks::INT) AS link_clicks'),
                DB::raw('sum(spend::DECIMAL) AS spend'),
                DB::raw('sum(leads_cnt) AS leads_cnt'),
            ])
            ->whereBetween('date', [$this->since, $this->until])
            ->whereNotNull('offer_id')
            ->notEmptyWhereIn('offer_id', $this->offers)
            ->notEmptyWhereIn('user_id', $this->users)
            ->when($this->users, function ($query) {
                return $query->whereIn('account_id', Account::forUsers($this->users)->pluck('account_id'));
            })
            ->groupBy('date', 'offer_id');
    }

    /**
     * Get a heading row for the report
     *
     * @return array
     */
    protected function headers()
    {
        return Headers::FULL;
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
            $cpl = $item->leads_cnt > 0 ? $item->spend / $item->leads_cnt : 0;
            $officeCost = $cpl * $item->leads_count;
            $cpc = $item->link_clicks > 0 ? $item->spend / $item->link_clicks : 0;
            $officeClicks = $cpc > 0 ? $officeCost / $cpc : 0;

            $rev = $item->ftd * 400;
            $profit = $rev - $officeCost;

            return [
                Fields::DATE          => $item->date->toDateString(),
                Fields::DESK          => optional($this->allOffices->get($item->office_id))->name ?? 'Без офиса',
                Fields::OFFER         => optional($this->allOffers->get($item->offer_id))->name ?? 'Без офера',
                Fields::CLICKS        => round($officeClicks, 2),
                Fields::CPM           => sprintf(
                    '$ %s',
                    $item->impressions > 0 ? round($item->spend / ($item->impressions / 1000), 2) : 0
                ),
                Fields::CPC               => sprintf('$ %s', round($cpc, 2)),
                Fields::CTR               => sprintf(
                    '%s %%',
                    $this->percentage($item->link_clicks, $item->impressions)
                ),
                Fields::CRPL              => sprintf('%s %%', $this->percentage($item->lp_clicks, $item->lp_views)),
                Fields::CRLP              => sprintf('%s %%', $this->percentage($item->bleads, $item->lp_views)),
                Fields::CR                => sprintf('%s %%', $this->percentage($item->bleads, $item->bclicks)),
                Fields::LEADS             => $item->leads_count,
                Fields::FTD               => $item->ftd,
                Fields::FTD_PERCENT       => sprintf('%s %%', $this->percentage($item->ftd, $item->leads_count)),
                Fields::REVENUE           => sprintf('$ %s', $rev),
                Fields::BCPL              => sprintf('$ %s', round($cpl, 2)),
                Fields::BCOST             => sprintf('$ %s', round($officeCost, 2)),
                Fields::PROFIT            => sprintf('$ %s', round($profit, 2)),
                Fields::ROI               => sprintf('%s %%', $this->percentage($profit, $officeCost)),
            ];
        });
    }

    protected function percentage($one, $two)
    {
        if ($two == 0) {
            return 0;
        }

        return round($one / $two * 100, 2);
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
        $cpl          = $data->sum('leads_cnt') > 0 ? $data->sum('spend') / $data->sum('leads_cnt') : 0;
        $officeCost   = $cpl * $data->sum('leads_count');
        $cpc          = $data->sum('link_clicks') > 0 ? $data->sum('spend') / $data->sum('link_clicks') : 0;
        $officeClicks = $cpc > 0 ? $officeCost / $cpc : 0;

        $rev    = $data->sum('ftd') * 400;
        $profit = $rev - $officeCost;

        return [
            Fields::DATE          => '',
            Fields::DESK          => '',
            Fields::OFFER         => '',
            Fields::CLICKS        => round($officeClicks, 2),
            Fields::CPM           => sprintf(
                '$ %s',
                $data->sum('impressions') > 0
                    ? round($data->sum('spend') / ($data->sum('impressions') / 1000), 2)
                    : 0
            ),
            Fields::CPC           => sprintf('$ %s', round($cpc, 2)),
            Fields::CTR           => sprintf(
                '%s %%',
                $this->percentage($data->sum('link_clicks'), $data->sum('impressions'))
            ),
            Fields::CRPL              => sprintf(
                '%s %%',
                $this->percentage($data->sum('lp_clicks'), $data->sum('lp_views'))
            ),
            Fields::CRLP              => sprintf(
                '%s %%',
                $this->percentage($data->sum('bleads'), $data->sum('lp_views'))
            ),
            Fields::CR                => sprintf(
                '%s %%',
                $this->percentage($data->sum('bleads'), $data->sum('bclicks'))
            ),
            Fields::LEADS             => $data->sum('leads_count'),
            Fields::FTD               => $data->sum('ftd'),
            Fields::FTD_PERCENT       => sprintf(
                '%s %%',
                $this->percentage($data->sum('ftd'), $data->sum('leads_count'))
            ),
            Fields::REVENUE           => sprintf('$ %s', $rev),
            Fields::BCPL              => sprintf('$ %s', round($cpl, 2)),
            Fields::BCOST             => sprintf('$ %s', round($officeCost, 2)),
            Fields::PROFIT            => sprintf('$ %s', round($profit, 2)),
            Fields::ROI               => sprintf('%s %%', $this->percentage($profit, $officeCost)),
        ];
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\DailyOpt\Report
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
     * @return \App\Reports\DailyOpt\Report
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
     * @return \App\Reports\DailyOpt\Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Filter results for specific offices
     *
     * @param array|null $offices
     *
     * @return \App\Reports\DailyOpt\Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = $offices;

        return $this;
    }

    /**
     * Limit report to certain users
     *
     * @param array|null $users
     *
     * @return \App\Reports\DailyOpt\Report
     */
    public function forUsers($users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @param array|null $groups
     *
     * @return \App\Reports\DailyOpt\Report
     */
    public function forOfficeGroups($groups = null)
    {
        $this->officeGroups = $groups;

        return $this;
    }
}
