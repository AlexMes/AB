<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\OfficesStats\Report;
use Illuminate\Http\Request;

class OfficeStats extends Controller
{
    /**
     * Load quick stats for dashboard
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
