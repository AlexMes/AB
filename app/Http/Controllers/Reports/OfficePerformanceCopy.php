<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\OfficePerformanceCopy\Report;
use Illuminate\Http\Request;

class OfficePerformanceCopy extends Controller
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
