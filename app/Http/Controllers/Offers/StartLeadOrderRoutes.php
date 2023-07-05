<?php

namespace App\Http\Controllers\Offers;

use App\AdsBoard;
use App\Http\Controllers\Controller;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Http\Request;

class StartLeadOrderRoutes extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Offer $offer, Request $request)
    {
        $this->authorize('viewAny', LeadOrderRoute::class);

        AdsBoard::report(
            sprintf(
                "Offer #%s started by %s",
                $offer->id,
                $request->user()->name
            )
        );

        $offer->routes()->visible()->update(['status' => LeadOrderRoute::STATUS_ACTIVE]);

        return response(null, 202);
    }
}
