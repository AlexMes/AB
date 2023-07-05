<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Http\Requests\MassTransferAssignments as Request;
use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use App\Manager;

class MassTransferAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\CRM\Http\Requests\MassTransferAssignments $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $targetManager = Manager::find($request->input('manager_id'));

        LeadOrderAssignment::visible()
            ->whereIn('lead_order_assignments.id', $request->input('assignments'))
            ->get()
            ->each
            ->transfer($targetManager);

        return back()->with('message', 'Assignments have been transferred.');
    }
}
