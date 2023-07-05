<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\DefaultExport as Export;
use App\Http\Controllers\Controller;
use App\Reports\MonthlyDtdNOA\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyDtdNOA extends Controller
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
        return Excel::download(new Export(Report::fromRequest($request)), 'monthly-dtd-noa.xlsx');
    }
}
