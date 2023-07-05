<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeadsOrderRoute;
use App\LeadsOrder;
use Illuminate\Validation\ValidationException;

class LeadsOrdersRoutesController extends Controller
{
    /**
     * Authorize access to leads order
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(LeadsOrder::class, 'leadsOrder');
    }

    /**
     * Index order routes
     *
     * @param LeadsOrder $order
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(LeadsOrder $order)
    {
        return $order->routes()->withTrashed()->get();
    }

    /**
     * Create new route in order
     *
     * @param LeadsOrder                               $order
     * @param \App\Http\Requests\CreateLeadsOrderRoute $request
     *
     * @return \Illuminate\Validation\ValidationException
     */
    public function store(LeadsOrder $order, CreateLeadsOrderRoute $request)
    {
        $duplicate = $order->routes()
            ->where('offer_id', $request->get('offer_id'))
            ->where('manager_id', $request->get('manager_id'))
            ->first();

        if ($duplicate) {
            return ValidationException::withMessages([
                'message' => 'Duplicated route.'
            ]);
        }

        return $order->routes()->create($request->validated());
    }
}
