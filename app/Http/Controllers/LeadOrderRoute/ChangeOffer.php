<?php

namespace App\Http\Controllers\LeadOrderRoute;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeLeadOrderRouteOffer;
use App\LeadOrderRoute;
use App\Offer;

class ChangeOffer extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ChangeLeadOrderRouteOffer $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, ChangeLeadOrderRouteOffer $request)
    {
        $route->changeOffer(Offer::find($request->input('offer_id')));

        return response(null, 202);
    }
}
