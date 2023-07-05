<?php

namespace App\Reports\AdDisapprovalReason;

use App\Facebook\Ad;
use App\Facebook\AdDisapproval;
use App\Insights;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\AdDisapprovalReason
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
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\AdDisapprovalReason\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
        ]);
    }

    /**
     * Report constructor.
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
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\AdDisapprovalReason\Report
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
     * @return \App\Reports\AdDisapprovalReason\Report
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

    /**
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->aggregate();

        return [
            'headers'  => ['Buyer', 'Created','Banned', 'Passed', 'Cloak','U.trust', 'Other'],
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return AdDisapproval[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return Ad::query()
            ->select([
                DB::raw('users.name as buyer'),
                DB::raw('count(facebook_ads.id) as created'),
                DB::raw("count(case when facebook_ads.reject_reason IS NOT NULL then 1 end) as banned"),
                DB::raw("count(case when facebook_ads.reject_reason = 'CLOAKING' then 1 end) as cloak"),
                DB::raw("count(case when facebook_ads.reject_reason = 'USER_TRUST' then 1 end) as user_trust"),
                DB::raw("count(case when facebook_ads.reject_reason != 'USER_TRUST' and facebook_ads.reject_reason != 'CLOAKING' and facebook_ads.reject_reason is not null then 1 end) as other"),
                DB::raw("count(case when ad_costs.sum > 0 then 1 end) as passed"),
            ])
            ->leftJoin('facebook_ads_accounts', 'facebook_ads.account_id', '=', 'facebook_ads_accounts.account_id')
            ->leftJoin('facebook_profiles', 'facebook_ads_accounts.profile_id', '=', 'facebook_profiles.id')
            ->leftJoin('users', 'facebook_profiles.user_id', '=', 'users.id')
            ->leftJoinSub(Insights::select(['ad_id', DB::raw('sum(spend::decimal)')])->groupBy('ad_id'), 'ad_costs', fn ($join) => $join->on('facebook_ads.id', '=', 'ad_costs.ad_id'))
            ->when(
                $this->since && $this->until,
                fn ($q) => $q->whereBetween(DB::raw('facebook_ads.created_at::date'), [$this->since, $this->until])
            )
            ->groupBy('buyer')
            ->get();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(fn ($row) => [
            'buyer'      => $row['buyer'] ?? 'Unknown',
            'created'    => $row['created'],
            'passed'     => sprintf("%s / %s%%", $row['passed'], percentage($row['passed'], $row['created'])),
            'banned'     => sprintf("%s / %s%%", $row['banned'], percentage($row['banned'], $row['created'])),
            'cloak'      => sprintf("%s / %s%%", $row['cloak'], percentage($row['cloak'], $row['created'])),
            'user_trust' => sprintf("%s / %s%%", $row['user_trust'], percentage($row['user_trust'], $row['created'])),
            'other'      => sprintf("%s / %s%%", $row['other'], percentage($row['other'], $row['created'])),
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary($data)
    {
        return [
            'buyer'      => 'Total',
            'created'    => $data->sum('created'),
            'passed'     => sprintf("%s / %s%%", $data->sum('passed'), percentage($data->sum('passed'), $data->sum('created'))),
            'banned'     => sprintf("%s / %s%%", $data->sum('banned'), percentage($data->sum('banned'), $data->sum('created'))),
            'cloak'      => sprintf("%s / %s%%", $data->sum('cloak'), percentage($data->sum('cloak'), $data->sum('created'))),
            'user_trust' => sprintf("%s / %s%%", $data->sum('user_trust'), percentage($data->sum('user_trust'), $data->sum('created'))),
            'other'      => sprintf("%s / %s%%", $data->sum('other'), percentage($data->sum('other'), $data->sum('created'))),
        ];
    }
}
