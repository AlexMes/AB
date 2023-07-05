<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferLeadOrderAssignment;
use App\LeadOrderAssignment;
use App\Manager;

class TransferAssignment extends Controller
{
    /**
     * Handle the incoming request.
     *
     *
     * @param LeadOrderAssignment         $assignment
     * @param TransferLeadOrderAssignment $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadOrderAssignment $assignment, TransferLeadOrderAssignment $request)
    {
        $assignment->transfer(Manager::find($request->input('manager_id')));

        return response()->json($assignment->loadMissing(['lead']), 202);
    }
}
