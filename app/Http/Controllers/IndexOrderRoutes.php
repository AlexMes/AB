<?php

namespace App\Http\Controllers;

use App\LeadsOrder;
use Illuminate\Http\Request;

class IndexOrderRoutes extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order)
    {
        $this->authorize('view', $order);

        return $order->routes()
            ->visible()
            ->withTrashed()
            ->with(['offer','manager','destination'])
            ->orderByRaw('offer_id, deleted_at ASC NULLS FIRST, manager_id')
            ->get();
    }
}
