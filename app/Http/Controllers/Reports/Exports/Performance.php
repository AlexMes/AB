<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\Performance as PerformanceExport;
use App\Http\Controllers\Controller;
use App\Reports\Performance\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Performance extends Controller
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
        return Excel::download(new PerformanceExport(Report::fromRequest($request)), 'performance.xlsx');
    }
}
