<?php

namespace App\Http\Controllers;

use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Http\Request;

class LeadsOrdersLeftoversStats extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', LeadOrderRoute::class);

        return response()->json(Offer::allowed()
            ->select(['id', 'name'])
            ->leftovers()
            ->withAcceptedCount(['2020-04-07', now()->toDateString()])
            ->withOrderedCount()
            ->withReceivedCount(['2020-04-07', now()->toDateString()])
            ->withLeftoverCount(['2020-04-07', now()->toDateString()])
            ->orderBy('leftover', 'desc')
            ->get()
            ->reject(fn ($offer) => $offer->leftover < 1)
            ->values()
            ->toArray(), 200);
    }
}
