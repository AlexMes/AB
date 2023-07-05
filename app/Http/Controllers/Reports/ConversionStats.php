<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ConversionStats\Report;
use Illuminate\Http\Request;

class ConversionStats extends Controller
{
    /**
     * Load quick stats for dashboard
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\ConversionStats\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
