<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLeadsOrderRoute;
use App\LeadOrderRoute;
use Illuminate\Http\Request;

class UpdateOrderRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadOrderRoute                      $route
     * @param \App\Http\Requests\UpdateLeadsOrderRoute $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, UpdateLeadsOrderRoute $request)
    {
        // $this->authorize('update', $route->order);

        return tap($route)->update($request->validated())->load(['destination', 'manager', 'offer']);
    }
}
