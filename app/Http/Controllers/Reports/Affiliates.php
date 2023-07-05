<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Affiliates\Report;
use Illuminate\Http\Request;

class Affiliates extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report|\App\Reports\DailyOpt\Report|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
