<?php

namespace App\Gamble\Http\Controllers\Reports;

use App\Gamble\Reports\Performance\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Performance extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
