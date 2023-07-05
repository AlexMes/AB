<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ResellDoubles\Report;
use Illuminate\Http\Request;

class ResellDoubles extends Controller
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
