<?php

namespace App\Http\Controllers\LeadsOrder;

use App\AdsBoard;
use App\Http\Controllers\Controller;
use App\LeadsOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class Stop extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadsOrder          $order
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, Request $request)
    {
        $this->authorize('update', $order);

        $order->routes()->visible()->update(['leadsOrdered' => DB::raw('"leadsReceived"')]);

        AdsBoard::report(
            sprintf(
                "[Order #%s (%s)](%s) stopped by %s",
                $order->id,
                $order->office->name,
                URL::to(sprintf("/leads-orders/%s", $order->id), [], true),
                $request->user()->name
            )
        );

        return response()->json($order->append(['leadsOrdered', 'leadsReceived', 'progress']), 202);
    }
}
