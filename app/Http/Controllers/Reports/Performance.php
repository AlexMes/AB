<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Performance\Report;
use Illuminate\Http\Request;

class Performance extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Performance\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
