<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Http\Requests\UpdateOrderRouteAssignment as UpdateOrderRouteAssignmentRequest;
use App\LeadOrderAssignment;
use App\LeadPaymentCondition;
use Illuminate\Support\Arr;

class UpdateOrderRouteAssignment extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param UpdateOrderRouteAssignmentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(UpdateOrderRouteAssignmentRequest $request, LeadOrderAssignment $assignment)
    {
        $cpa = LeadPaymentCondition::getCpa($assignment->route->offer_id, $assignment->route->order->office_id);

        $assignment->update(Arr::except($request->validated(), $cpa !== null ? ['benefit'] : []));

        if ($assignment->status === 'Депозит') {
            /** @var Deposit $deposit */
            $deposit = Deposit::createFromAssignment($assignment);

            if ($cpa !== null && $request->filled('benefit')) {
                $deposit->updateBenefit($request->input('benefit'));
            }
        }

        return response()->json($assignment->loadMissing(['lead.ipAddress'])->append(['autologin', 'actual_benefit']));
    }
}
