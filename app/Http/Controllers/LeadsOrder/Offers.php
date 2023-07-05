<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\LeadsOrder;

class Offers extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Support\Collection
     */
    public function __invoke(LeadsOrder $order)
    {
        $order->loadMissing('routes.offer');

        return $order->routes()->with(['offer'])->visible()->get()->pluck('offer')->unique('id')->values();
    }
}
