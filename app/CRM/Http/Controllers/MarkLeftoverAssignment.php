<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Http\Requests\MarkLeftoverAssignment as MarkLeftoverAssignmentRequest;
use App\CRM\Http\Requests\MarkLeftoverAssignment as Request;
use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;

class MarkLeftoverAssignment extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\CRM\LeadOrderAssignment $assignment
     * @param Request                      $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadOrderAssignment $assignment, MarkLeftoverAssignmentRequest $request)
    {
        $assignment->lead->toLeftover();

        return back()->with('message', trans('crm::lead.leftover_success'));
    }
}
