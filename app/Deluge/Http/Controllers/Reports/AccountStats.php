<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Branch;
use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Http\Middlewares\CheckOffice;
use App\Deluge\Reports\AccountStats\Report;
use App\Http\Controllers\Controller;
use App\ManualGroup;
use App\Team;
use App\User;
use Illuminate\Http\Request;

class AccountStats extends Controller
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('deluge::reports.account-stats')
            ->with([
                'users'         => User::query()->withFacebookTraffic()->get(['id', 'name']),
                'groups'        => ManualGroup::visible()->get(['id', 'name']),
                'branches'      => Branch::query()->get(['id', 'name']),
                'teams'         => Team::query()->get(['id', 'name']),
                'report'        => Report::fromRequest($request)->toArray(),
            ]);
    }
}
