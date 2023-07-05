<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DailyOpt\Report;
use Illuminate\Http\Request;

class DailyOpt extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Daily\Report|Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
