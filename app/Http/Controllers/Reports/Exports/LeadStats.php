<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\LeadStats as LeadStatsExport;
use App\Http\Controllers\Controller;
use App\Reports\LeadStats\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadStats extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        return Excel::download(new LeadStatsExport(Report::fromRequest($request)), 'lead-stats.xlsx');
    }
}
