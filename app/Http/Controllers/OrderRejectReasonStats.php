<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Support\Facades\DB;

class OrderRejectReasonStats extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Order $order)
    {
        $data = $order->ads()
            ->select([
                'reject_reason',
                DB::raw('count(*) as cnt'),
            ])
            ->groupBy('reject_reason', 'order_id')
            ->orderBy('reject_reason')
            ->get();

        return $data
            ->reject(fn ($item) => $item->reject_reason === null)
            ->push(['reject_reason' => 'total', 'cnt' => $data->sum('cnt') ?? 0]);
    }
}
