<?php

namespace App\Http\Controllers;

use App\Facebook\Ad;
use App\Facebook\PaymentFail;
use Illuminate\Http\Request;

class PaymentFailsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Ad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return PaymentFail::query()->with(['account', 'user'])
            ->when($request->get('date'), function ($q) use ($request) {
                return $q->whereDate('created_at', $request->get('date'));
            })
            ->searchIn(['account_id'], $request->account)
            ->take(100)
            ->get();
    }
}
