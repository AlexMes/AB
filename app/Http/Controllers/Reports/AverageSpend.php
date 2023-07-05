<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\AverageSpend\Report;
use Illuminate\Http\Request;

class AverageSpend extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\AverageSpend\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
