<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\OfficePerformance\Report;
use Illuminate\Http\Request;

class OfficePerformance extends Controller
{
    /**
     * @param Request $request
     *
     * @return Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
