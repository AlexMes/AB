<?php

namespace App\Http\Controllers\LeadsOrder;

use App\AdsBoard;
use App\Http\Controllers\Controller;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class Start extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadsOrder          $order
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, Request $request)
    {
        $this->authorize('update', $order);

        $order->routes()->visible()->update(['status' => LeadOrderRoute::STATUS_ACTIVE]);

        AdsBoard::report(
            sprintf(
                "[Order #%s (%s)](%s) started by %s",
                $order->id,
                $order->office->name,
                URL::to(sprintf("/leads-orders/%s", $order->id), [], true),
                $request->user()->name
            )
        );

        return response(null, 204);
    }
}
