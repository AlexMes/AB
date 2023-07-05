<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\LeadStats\Report;
use Illuminate\Http\Request;

class LeadStats extends Controller
{
    /**
     * Handle the incoming request.
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
