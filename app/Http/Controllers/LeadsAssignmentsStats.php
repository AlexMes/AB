<?php

namespace App\Http\Controllers;

use App\Lead;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadsAssignmentsStats extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\LeadOrderRoute[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', LeadOrderRoute::class);

        $period = $request->input('date') ? json_decode($request->input('date'), true) : [];

        return Offer::allowed()
            ->select(['id', 'name', DB::raw('COALESCE(accepted,0) as accepted'),DB::raw('COALESCE(leftover,0) as leftover')])
            ->current()
            ->leftJoinSub(Lead::visible()
            ->valid()
            ->selectRaw('count(*) as accepted, offer_id')
            ->when(date_between($period), fn ($query) => $query->whereBetween(DB::raw('created_at::date'), date_between($period)))
            ->unless(date_between($period), fn ($query) => $query->whereDate('created_at', now()))->groupBy('offer_id')->havingRaw('count(*) > 0'), 'accepted', 'offers.id', '=', 'accepted.offer_id')
            ->leftJoinSub(Lead::visible()
            ->selectRaw('count(*) as leftover, offer_id')
            ->leftovers(date_between($period))->groupBy('offer_id')->havingRaw('count(*) > 0'), 'leftover', 'offers.id', '=', 'leftover.offer_id')
            ->withOrderedCount($period)
            ->withReceivedCount($period)
            ->orderByRaw("case when vertical = 'Crypt' then 1 when vertical = 'Burj' then 2 else 3 end asc")
            ->get()
            ->reject(fn (Offer $offer) => $offer->leftover === 0 && $offer->ordered === null && $offer->accepted === 0)
            ->values()
            ->toArray();
    }
}
