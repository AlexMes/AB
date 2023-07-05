<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Placements\Report;
use Illuminate\Http\Request;

class Placements extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Placements\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
