<?php

namespace App\Deluge\Http\Controllers\Reports\Exports;

use App\Deluge\Exports\Reports\BuyerCosts as Export;
use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Http\Middlewares\CheckOffice;
use App\Deluge\Reports\BuyerCosts\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BuyerCosts extends Controller
{
    /**
     * PerformanceReport constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([CheckOffice::class, AccessToReports::class]);
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        $report = Report::fromRequest($request)->groupByAccount(true);

        return Excel::download(new Export($report), 'buyer-costs.xlsx');
    }
}
