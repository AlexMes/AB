<?php

namespace App\CRM\Http\Controllers;

use App\Branch;
use App\CRM\Queries\ManagerStatistic as Statistic;
use App\CRM\Status;
use App\Http\Controllers\Controller;
use App\Manager;
use Closure;
use Illuminate\Http\Request;

class OfficeStatistic extends Controller
{
    /**
     * @var \App\CRM\Queries\ManagerStatistic
     */
    protected Statistic $statistic;

    /**
     * Statistic constructor.
     *
     * @param \App\CRM\Queries\ManagerStatistic $statistic
     */
    public function __construct(Statistic $statistic)
    {
        $this->middleware(function ($request, Closure $next) {
            if (auth('web')->check() && auth('web')->user()->hasRole(['admin','support'])) {
                return $next($request);
            }
            if (auth('crm')->check() && auth('crm')->user()->hasElevatedPrivileges()) {
                return $next($request);
            }

            abort(403, 'This action is not authorized.');
        });

        $this->statistic = $statistic;
    }

    /**
     * Display manager statistics
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('crm::office-statistic')
            ->with([
                /*'offices'               => Office::visible()
                    ->where(fn ($query) => $query->whereNull('destination_id')
                    ->orWhereIn('destination_id', LeadDestination::whereIn('driver', [LeadDestinationDriver::INVESTEX, LeadDestinationDriver::HOTLEADS])->pluck('id')))

                    ->get(),*/
                'managers'    => Manager::visible()
                    /*->when(
                        $request->input('office'),
                        fn ($query) => $query->whereIn('office_id', Arr::wrap($request->input('office')))
                    )*/
                    ->get(['id', 'name']),
                'offers'      => $request->user()->offers(),
                'statuses'    => Status::all(),
                'branches'    => Branch::allowed()->get(['id', 'name']),
                'statistics'  => $this
                    ->statistic
                    ->since($request->input('since'))
                    ->until($request->input('until'))
                    ->sinceRegistration($request->input('since_registration'))
                    ->untilRegistration($request->input('until_registration'))
                    /*->forOffice($request->input('office'))*/
                    ->forOfferType($request->input('offerType'))
                    ->forOffer($request->input('offer'))
                    ->forManager($request->input('manager'))
                    ->forBranch($request->input('branch'))
                    ->withoutOfficeDestination()
                    ->orderBy($request->input('sort'), $request->boolean('desc', false))
                    ->groupByOffice()
                    ->get(),
            ]);
    }
}
