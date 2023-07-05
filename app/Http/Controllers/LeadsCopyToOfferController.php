<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leads\CopyToOffer;
use App\Jobs\CopyLeadsToOffer;

class LeadsCopyToOfferController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CopyToOffer $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CopyToOffer $request)
    {
        CopyLeadsToOffer::dispatch($request->validated());

        return response()->noContent();
    }
}
