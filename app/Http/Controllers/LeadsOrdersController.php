<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeadsOrder;
use App\Http\Requests\UpdateLeadsOrder;
use App\LeadsOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LeadsOrdersController extends Controller
{
    /**
     * Construct controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->authorizeResource(LeadsOrder::class);
    }

    /**
     * Index leads orders
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return LeadsOrder::visible()
            ->with(['office', 'branch'])
            ->withReceived()
            ->withOrdered()
            ->withLastReceived()
            ->withPausedRoutesCount()
            ->withDeliveryPercent()
            ->withDeliverAtAssignmentCount()
            ->withMissedDeliverAtAssignmentCount(now()->subMinutes(15))
            ->withCount(['routes'])
            ->when($request->input('date'), function (Builder $query) use ($request) {
                $period = json_decode($request->input('date'), true);
                $query->whereBetween('date', $period);
            })
            ->orderByDesc('leads_orders.id')
            ->when($request->boolean('all'), fn ($q) => $q->get(), fn ($q) => $q->paginate());
    }

    /**
     * Index leads orders
     *
     * @param \App\LeadsOrder $leadsOrder
     *
     * @return \App\LeadsOrder
     */
    public function show(LeadsOrder $leadsOrder)
    {
        return $leadsOrder
            ->load(['office', 'destination', 'branch'])
            ->append(['progress', 'activeRoutesCount', 'pausedRoutesCount', 'finishedRoutesCount', 'deliveryPercent', 'deliverCount', 'deliverConfirmedCount']);
    }

    /**
     * Store new order in database
     *
     * @param Request $request
     *
     * @return LeadsOrder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(CreateLeadsOrder $request)
    {
        $order = LeadsOrder::query()
            ->where('branch_id', $request->input('branch_id'))
            ->where('office_id', $request->get('office_id'))
            ->whereDate('date', $request->get('date'))
            ->first();

        if ($order !== null) {
            return redirect()->route('leads-orders.show', $order);
        }

        return LeadsOrder::create($request->validated());
    }

    /**
     * Store new order in database
     *
     * @param Request $request
     *
     * @return void
     */
    public function update(LeadsOrder $leadsOrder, UpdateLeadsOrder $request)
    {
        return tap($leadsOrder)->update($request->validated());
    }

    /**
     * Store new order in database
     *
     * @param \App\LeadsOrder $leadsOrder
     *
     * @throws \Exception
     *
     * @return void
     */
    public function destroy(LeadsOrder $leadsOrder)
    {
        $leadsOrder->delete();

        return response()->noContent();
    }
}
