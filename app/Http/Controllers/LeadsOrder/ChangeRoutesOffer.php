<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeLeadOrderOffer;
use App\LeadsOrder;
use App\Offer;

class ChangeRoutesOffer extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ChangeLeadOrderOffer $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, ChangeLeadOrderOffer $request)
    {
        $order->routes()
            ->visible()
            ->where('offer_id', $request->input('from_offer_id'))
            ->incomplete()
            ->get()
            ->each
            ->changeOffer(Offer::find($request->input('to_offer_id')));

        return response(null, 202);
    }
}
