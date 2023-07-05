<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Regions\Report;
use Illuminate\Http\Request;

class Regions extends Controller
{
    /**
     * Build and render region report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Regions\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
