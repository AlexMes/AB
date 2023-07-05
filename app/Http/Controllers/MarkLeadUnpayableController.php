<?php

namespace App\Http\Controllers;

use App\Http\Requests\MakeLeadUnpayable;
use App\Lead;
use App\LeadOrderAssignment;

class MarkLeadUnpayableController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Lead              $lead
     * @param MakeLeadUnpayable $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Lead $lead, MakeLeadUnpayable $request)
    {
        $lead->assignments->each(fn (LeadOrderAssignment $assignment) => $assignment->markAsUnpayable());

        return response()->noContent(202);
    }
}
