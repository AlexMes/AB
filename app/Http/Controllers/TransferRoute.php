<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferLeadOrderRoute;
use App\LeadOrderRoute;
use App\Manager;

class TransferRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadOrderRoute                       $route
     * @param \App\Http\Requests\TransferLeadOrderRoute $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, TransferLeadOrderRoute $request)
    {
        $route->transfer(Manager::find($request->input('manager_id')));

        return response()->json(null, 202);
    }
}
