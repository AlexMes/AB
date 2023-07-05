<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Http\Middlewares\CheckOffice;
use App\Deluge\Reports\BuyerCosts\Report;
use App\Http\Controllers\Controller;
use App\ManualBundle;
use App\Offer;
use App\Team;
use App\User;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('deluge::reports.buyer-costs')
            ->with([
                'offers'        => Offer::allowed()->get(['id', 'name']),
                'users'         => User::visible()->withFacebookTraffic()->get(['id', 'name']),
                'teams'         => Team::query()->get(['id', 'name']),
                'bundles'       => ManualBundle::allowedOffers()->orderBy('name', 'asc')->get(['id', 'name']),
                'report'        => Report::fromRequest($request)->toArray(),
            ]);
    }
}
