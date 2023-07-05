<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkAssignmentUnpayable;
use App\LeadOrderAssignment;

class MarkAssignmentUnpayableController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param LeadOrderAssignment     $assignment
     * @param MarkAssignmentUnpayable $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadOrderAssignment $assignment, MarkAssignmentUnpayable $request)
    {
        $assignment->markAsUnpayable();

        return response()->json($assignment->loadMissing(['lead.ipAddress']), 202);
    }
}
