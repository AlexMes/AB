<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\LeadOrderAssignment;
use App\LeadsOrder;
use Illuminate\Http\Request;

class RemoveDelayedAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, Request $request)
    {
        $this->authorize('update', $order);

        LeadOrderAssignment::pendingDelayed()
            ->whereIn('route_id', $order->routes()->visible()->pluck('lead_order_routes.id'))
            ->each(fn (LeadOrderAssignment $assignment) => $assignment->remove());

        return response()->json($order->append(['leadsOrdered', 'leadsReceived', 'progress']), 202);
    }
}
