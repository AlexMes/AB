<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\CloneLeadOrder as CloneRequest;
use App\LeadsOrder;
use Illuminate\Support\Carbon;

class CloneLeadOrder extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\CloneLeadOrder $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadsOrder $order, CloneRequest $request)
    {
        $order->cloneToDate(Carbon::parse($request->input('date')));

        return response(null, 202);
    }
}
