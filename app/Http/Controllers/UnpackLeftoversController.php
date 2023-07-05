<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnpackLeftovers;
use App\Lead;
use App\Offer;

class UnpackLeftoversController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param UnpackLeftovers $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UnpackLeftovers $request)
    {
        foreach (
            Offer::leftovers()
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
                    $lead->fromLeftover();
                });
        }

        return response()->noContent(202);
    }
}
