<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Http\Requests\TransferAssignment as TransferAssignmentRequest;
use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use App\Manager;
use Illuminate\Support\Facades\Log;

class TransferAssignment extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param LeadOrderAssignment       $assignment
     * @param TransferAssignmentRequest $request
     *
     * @throws \Throwable
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     *
     */
    public function __invoke(LeadOrderAssignment $assignment, TransferAssignmentRequest $request)
    {
        $targetManager = Manager::find($request->input('manager_id'));
        $oldManager    = $assignment->route->manager;

        $assignment->transfer($targetManager);

        Log::channel('crm-transferred-assignments')->debug(
            class_basename(auth()->user()) . " #" . auth()->id() . " (" . auth()->user()->name . ")" .
            " transferred assignment #{$assignment->id}" .
            " from manager #{$oldManager->id} ({$oldManager->name})" .
            " to #{$targetManager->id} ({$targetManager->name})"
        );

        return redirect()
            ->route('crm.assignments.index')
            ->with('message', 'Lead has been transferred successful.');
    }
}
