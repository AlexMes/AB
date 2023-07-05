<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Reports\LeadStats\Report;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Office;
use App\OfficeGroup;
use Illuminate\Http\Request;

class LeadStats extends Controller
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
        return view('deluge::reports.lead-stats')
            ->with([
                'offers'       => Offer::allowed()->get(['id', 'name']),
                'offices'      => Office::query()->get(['id', 'name']),
                'officeGroups' => OfficeGroup::visible()->get(['id', 'name']),
                'report'       => Report::fromRequest($request)->toArray(),
            ]);
    }
}
