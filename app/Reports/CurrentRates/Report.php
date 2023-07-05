<?php

namespace App\Reports\CurrentRates;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class Report implements Arrayable, Responsable
{
    /**
     * @var Request
     */
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get array representation of the report
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'threeDayConversion' => ThreeDayConversion::fromRequest($this->request)->toArray(),
            'planFact'           => PlanFact::fromRequest($this->request)->toArray(),
            'dailyForMonth'      => (new DailyForMonth())->toArray(),
            'daily'              => Daily::fromRequest($this->request)->toArray(),
            'performanceDay'     => (new Performance())->toArray(),
            'performance3Days'   => (new Performance())
                ->since(now()->subDays(3))
                ->until(now()->subDays(1))
                ->toArray(),
        ];
    }

    /**
     * Get response implementation of the report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray());
    }
}
