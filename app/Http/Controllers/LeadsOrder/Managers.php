<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\LeadsOrder;

class Managers extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Support\Collection
     */
    public function __invoke(LeadsOrder $order)
    {
        $order->loadMissing('routes.manager');

        return $order->routes->pluck('manager')->unique('id')->values();
    }
}
