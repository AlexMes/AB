<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Conversion\Report;
use Illuminate\Http\Request;

class Conversion extends Controller
{
    /**
     * Build and render conversion report
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
