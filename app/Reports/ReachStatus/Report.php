<?php

namespace App\Reports\ReachStatus;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @var \Illuminate\Support\Collection
     */
    protected $orders;


    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\ReachStatus\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
            'orders'   => $request->get('orders'),
        ]);
    }

    /**
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->forOrders($settings['orders'] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $users = $this->getReportData();

        return [
            'headers' => ['buyer', 'Прошли %', 'Не прошли %', 'Бан %', 'Без статуса %', 'Всего', 'Lifetime'],
            'summary' => $this->summary($users),
            'rows'    => $this->rows($users)->toArray(),
            'period'  => [
                'since' => null,
                'until' => null,
            ]
        ];
    }

    /**
     * Get rows
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(function ($item) {
            return [
                'name'      => $item->name,
                'passed'    => sprintf('%s %%', $this->percentage($item->passed, $item->total)),
                'missed'    => sprintf('%s %%', $this->percentage($item->missed, $item->total)),
                'banned'    => sprintf('%s %%', $this->percentage($item->banned, $item->total)),
                'none'      => sprintf('%s %%', $this->percentage($item->none, $item->total)),
                'total'     => $item->total,
                'liftetime' => $this->getLifetime($item->lifetime),
            ];
        });
    }

    /**
     * Get summary
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary($data)
    {
        $total = $data->sum('total');

        return [
            'name'      => 'ИТОГО',
            'passed'    => sprintf('%s %%', $this->percentage($data->sum('passed'), $total)),
            'missed'    => sprintf('%s %%', $this->percentage($data->sum('missed'), $total)),
            'banned'    => sprintf('%s %%', $this->percentage($data->sum('banned'), $total)),
            'none'      => sprintf('%s %%', $this->percentage($data->sum('none'), $total)),
            'total'     => $total,
            'liftetime' => $this->getLifetime($data->sum('lifetime')),
        ];
    }

    protected function getLifetime($seconds = 0)
    {
        $result = $seconds / 60;

        return sprintf('%s минут', round($result, 0));
    }

    protected function percentage($one, $two)
    {
        if ($two) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
    }

    /**
     * Report data, formatted by database
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return User::query()
            ->join('domains', function ($join) {
                $join->on('users.id', '=', 'domains.user_id')
//                    ->whereBetween('domains.created_at', [
//                        $this->since->startOfDay()->toDateTimeString(),
//                        $this->until->endOfDay()->toDateTimeString()
//                    ])
                    ->notEmptyWhereIn('domains.order_id', $this->orders->all());
            })
            ->select([
                'users.id',
                'users.name',
                DB::raw("count(case when domains.reach_status = 'passed' then 1 end) as passed"),
                DB::raw("count(case when domains.reach_status = 'missed' then 1 end) as missed"),
                DB::raw("count(case when domains.reach_status = 'banned' then 1 end) as banned"),
                DB::raw("count(case when domains.reach_status IS null then 1 end) as none"),
                DB::raw("count(users.id) as total"),
                DB::raw("EXTRACT(EPOCH FROM SUM(domains.failed_at - domains.created_at))::int as lifetime"),
            ])
            ->groupBy('users.id')
            ->orderBy('users.name')
            ->get();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\ReachStatus\Report
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
     * @return \App\Reports\ReachStatus\Report
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
     * @param null $orders
     *
     * @return $this
     */
    public function forOrders($orders = null)
    {
        $this->orders = collect($orders);

        return $this;
    }
}
