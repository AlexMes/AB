<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Daily\Report;
use Illuminate\Http\Request;

class Daily extends Controller
{
    /**
     * Build and render daily report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Daily\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
