<?php

namespace App\Http\Controllers\LeadOrderRoute;

use App\Http\Controllers\Controller;
use App\LeadOrderRoute;
use Illuminate\Http\Request;

class Stop extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, Request $request)
    {
        $this->authorize('update', $route->order);

        return response()->json(tap($route)->update(['leadsOrdered' => $route->leadsReceived]), 202);
    }
}
