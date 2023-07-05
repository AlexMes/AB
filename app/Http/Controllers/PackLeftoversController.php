<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackLeftovers;
use App\Lead;
use App\Offer;

class PackLeftoversController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param PackLeftovers $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PackLeftovers $request)
    {
        foreach (
            Offer::current()
                ->whereIn(
                    'id',
                    Lead::visible()->leftovers($request->input('date'))
                        ->notEmptyWhereIn('offer_id', $request->input('offer_id'))
                        ->pluck('offer_id')
                )->get() as $offer
        ) {
            Lead::visible()->leftovers($request->input('date'))
                ->where('offer_id', $offer->id)
                ->each(function (Lead $lead) {
                    $lead->toLeftover();
                });
        }

        return response()->noContent(202);
    }
}
