<?php

namespace App\Http\Controllers;

use App\LeadOrderRoute;

class RestoreLeadOrderRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $routeId
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke($routeId)
    {
        $route = LeadOrderRoute::onlyTrashed()->find($routeId);

        $this->authorize('restore', $route);

        $route->restore();

        return response()->noContent(200);
    }
}
