<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\MassStoreDomains as StoreDomainsRequest;
use App\Order;
use Illuminate\Support\Arr;

class MassStoreDomains extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Order                                      $order
     * @param \App\Http\Requests\Orders\MassStoreDomains $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Order $order, StoreDomainsRequest $request)
    {
        $data                  = Arr::except($request->validated(), ['urls']);
        $data['effectiveDate'] = $order->deadline_at;
        $data['sp_id']         = $order->sp_id;
        $data['bp_id']         = $order->bp_id;
        $data['cloak_id']      = $order->cloak_id;
        $data['landing_id']    = $order->landing_id;
        $data['offer_id']      = optional($order->landing)->offer_id;

        $domains = [];
        foreach ($request->input('urls') as $url) {
            $domains[] = array_merge($data, ['url' => $url]);
        }

        return response()->json($order->domains()->createMany($domains)->load(['user']), 201);
    }
}
