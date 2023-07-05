<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DesignerPerformance\Report;
use Illuminate\Http\Request;

class DesignerPerformance extends Controller
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
