<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeadsOrderRoute;
use App\LeadsOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreateOrderRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadsOrder          $order
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     */
    public function __invoke(LeadsOrder $order, CreateLeadsOrderRoute $request)
    {
        $this->authorize('update', $order);

        $duplicate = $order->routes()
            ->where('offer_id', $request->get('offer_id'))
            ->where('manager_id', $request->get('manager_id'))
            ->first();

        if ($duplicate !== null) {
            throw ValidationException::withMessages([
                'message' => 'Duplicated route.'
            ]);
        }

        return $order->routes()->create($request->validated());
    }
}
