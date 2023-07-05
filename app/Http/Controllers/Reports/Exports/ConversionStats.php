<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\ConversionStats as ConversionStatsExport;
use App\Http\Controllers\Controller;
use App\Reports\ConversionStats\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConversionStats extends Controller
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
        return Excel::download(new ConversionStatsExport(Report::fromRequest($request)), 'conversion-stats.xlsx');
    }
}
