<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Branch;
use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Reports\DesignerConversion\Report;
use App\Http\Controllers\Controller;
use App\Team;
use Illuminate\Http\Request;

class DesignerConversion extends Controller
{
    /**
     * PerformanceReport constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(AccessToReports::class);
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('deluge::reports.designer-conversion')
            ->with([
                'branches'      => Branch::query()->get(['id', 'name']),
                'teams'         => Team::query()->get(['id', 'name']),
                'report'        => Report::fromRequest($request)->toArray(),
            ]);
    }
}
