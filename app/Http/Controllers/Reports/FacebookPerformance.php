<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\FacebookPerformance\Report;
use Illuminate\Http\Request;

class FacebookPerformance extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\FacebookPerformance\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
