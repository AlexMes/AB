<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferLeadOrderRoutes;
use App\LeadsOrder;
use App\Manager;

class TransferRoutes extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\TransferLeadOrderRoutes $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, TransferLeadOrderRoutes $request)
    {
        $order->routes()
            ->visible()
            ->whereManagerId($request->input('from_manager_id'))
            ->get()
            ->each
            ->transfer(Manager::find($request->input('to_manager_id')));

        return response(null, 202);
    }
}
