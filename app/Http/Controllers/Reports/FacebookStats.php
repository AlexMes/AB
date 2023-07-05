<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\FacebookStats\Report;
use Illuminate\Http\Request;

class FacebookStats extends Controller
{
    /**
     * Load facebook stats for dashboard
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
