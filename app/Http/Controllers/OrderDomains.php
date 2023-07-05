<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OrderDomains extends Controller
{
    /**
     * Load single order domains
     *
     * @param \App\Order $order
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Order $order, Request $request)
    {
        $this->authorize('view', $order);

        return $order->domains()
            ->visible()
            ->when($request->has('user_id'), function (Builder $builder) use ($request) {
                if ($request->input('user_id') === 'all') {
                    return $builder;
                }

                if ($request->input('user_id') === null) {
                    return $builder->whereNull('user_id');
                }

                return $builder->whereIn('user_id', Arr::wrap($request->input('user_id')));
            })
            ->with(['user'])
            ->get();
    }
}
