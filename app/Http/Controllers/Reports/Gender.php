<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Gender\Report;
use Illuminate\Http\Request;

class Gender extends Controller
{
    /**
     * Build and render gender report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Gender\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
