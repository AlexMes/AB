<?php

namespace App\Reports\ProfilesBanned;

use App\Facebook\Profile;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\AccountsBanned
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
     * @return \App\Reports\ProfilesBanned\Report
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
     * @return \App\Reports\ProfilesBanned\Report
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
     * @return \App\Reports\ProfilesBanned\Report
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
            'headers'  => ['new', 'check', 'password', 'check%', 'password%'],
            'rows'     => [],
            'summary'  => [
                $data->sum('count') ?? 0,
                $data->sum('check') ?? 0,
                $data->sum('password') ?? 0,
                sprintf('%s %%', percentage($data->sum('check'), $data->sum('count'))),
                sprintf('%s %%', percentage($data->sum('password'), $data->sum('count'))),
            ],
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }


    /**
     * Load profiles
     *
     * @return Profile[]|\Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function aggregate()
    {
        return Profile::query()
            ->select([
                DB::raw('count(DISTINCT facebook_profiles.id) as count'),
                DB::raw('count(CASE WHEN issues.message = \'' . Profile::DISABLED_CHECKPOINT . '\' THEN 1 END) AS check'),
                DB::raw('count(CASE WHEN issues.message = \'' . Profile::DISABLED_PASSWORD . '\' THEN 1 END) AS password'),
            ])
            ->whereBetween('facebook_profiles.created_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->leftJoin('issues', function (JoinClause $join) {
                return $join->on('facebook_profiles.id', DB::raw('issues.issuable_id::int'))
                    ->where('issuable_type', 'profiles')
                    ->whereNull('fixed_at');
            })
            ->get();
    }
}
