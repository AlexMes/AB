<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\DefaultExport as Export;
use App\Http\Controllers\Controller;
use App\Reports\MonthlyOfficesNOA\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyOfficesNOA extends Controller
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
        return Excel::download(new Export(Report::fromRequest($request)), 'monthly-offices-noa.xlsx');
    }
}
