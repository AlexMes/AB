<?php

namespace App\Http\Controllers;

use App\Office;
use Illuminate\Http\Request;

class OfficeOrders extends Controller
{
    /**
     * Index office orders
     *
     * @param Office                   $office
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Office $office, Request $request)
    {
        return $office->orders()
            ->visible()
            ->withReceived()
            ->withOrdered()
            ->withLastReceived()
            ->orderByDesc('created_at')
            ->when($request->has('paginate'), fn ($q) => $q->paginate(25), fn ($q) => $q->get());
    }
}
