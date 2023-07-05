<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentDeliveryFails as Request;
use App\LeadOrderAssignment;
use Illuminate\Database\Eloquent\Builder;

class AssignmentDeliveryFails extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\AssignmentDeliveryFails $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(
            LeadOrderAssignment::query()
                ->with(['destination', 'route.manager'])
                ->when($request->input('managers'), function (Builder $builder) use ($request) {
                    return $builder->whereHas(
                        'route',
                        fn ($q) => $q->whereIn('manager_id', $request->input('managers'))
                    );
                })
                ->whereNotNull('delivery_failed')
                ->orderByDesc('created_at')
                ->paginate()
        );
    }
}
