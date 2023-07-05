<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Branch;
use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Http\Middlewares\CheckOffice;
use App\Deluge\Reports\Performance\Report;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualCampaign;
use App\ManualGroup;
use App\Offer;
use App\Team;
use App\User;
use Illuminate\Http\Request;

class Performance extends Controller
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
        return view('deluge::reports.performance')
            ->with([
                'offers'        => Offer::allowed()->get(['id', 'name']),
                'users'         => User::visible()->get(['id', 'name']),
                'teams'         => Team::visible()->get(['id', 'name']),
                'accounts'      => ManualAccount::visible()->get(['account_id', 'name']),
                'utm_campaigns' => ManualCampaign::visible()
                    ->allowedOffers()
                    ->select('utm_key')
                    ->distinct()
                    ->orderBy('utm_key')
                    ->pluck('utm_key'),
                'groups'        => ManualGroup::visible()->get(['id', 'name']),
                'branches'      => Branch::allowed()->get(['id', 'name']),
                'report'        => Report::fromRequest($request)->toArray(),
            ]);
    }
}
