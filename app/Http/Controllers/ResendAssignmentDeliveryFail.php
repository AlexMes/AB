<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentDeliveryFailResend;
use App\Jobs\DeliverAssignment;
use App\LeadOrderAssignment;

class ResendAssignmentDeliveryFail extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\AssignmentDeliveryFailResend $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadOrderAssignment $assignment, AssignmentDeliveryFailResend $request)
    {
        $assignment->update([
            'is_live' => false,
        ]);

        DeliverAssignment::dispatch($assignment);
        $assignment->recordDestinationId();

        return response()->noContent();
    }
}
