<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\TransferDomains;
use App\Order;

class TransferOrderDomains extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Order           $order
     * @param TransferDomains $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Order $order, TransferDomains $request)
    {
        $order->domains()
            ->whereIn('id', $request->input('domain_ids'))
            ->get()
            ->each
            ->transfer(Order::find($request->input('order_id')));

        return response()->noContent();
    }
}
