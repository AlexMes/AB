<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\QuickStats\Report;
use Illuminate\Http\Request;

class QuickStats extends Controller
{
    /**
     * Load quick stats for dashboard
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\QuickStats\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
