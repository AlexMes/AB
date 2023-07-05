<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\TrafficLoss\Report;
use Illuminate\Http\Request;

class TrafficLoss extends Controller
{
    /**
     * Load traffic loss report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\TrafficLoss\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
