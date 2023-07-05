<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\CreateOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrdersController extends Controller
{
    /**
     * OrdersController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Order::query()
            ->searchIn(['id'], $request->get('search'))
            ->with(['sp', 'bp', 'registrar', 'landing', 'cloak', 'hosting'])
            ->orderByDesc('id')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Orders\CreateOrderRequest $request
     *
     * @return \App\Order|\Illuminate\Database\Eloquent\Model
     */
    public function store(CreateOrderRequest $request)
    {
        return Order::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     *
     * @return Order
     */
    public function show(Order $order)
    {
        return $order->load(['sp', 'bp', 'registrar', 'landing', 'cloak', 'hosting']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOrderRequest $request
     * @param Order              $order
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        if ($order->isCompleted()) {
            return response()->json([
                'message' => 'You cant update completed orders, create the new one instead',
            ], 422);
        }
        if ($request->get('links_count') < $order->links_done_count) {
            throw ValidationException::withMessages([
                'links_count' => 'Impossible to order less links, than already done.'
            ]);
        }

        return response()->json(tap($order)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response('Forbidden', 403);
    }
}
