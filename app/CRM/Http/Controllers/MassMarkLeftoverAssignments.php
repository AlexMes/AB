<?php

namespace App\CRM\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MassMarkLeftoverAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('massMarkAsLeftover', LeadOrderAssignment::class);

        LeadOrderAssignment::query()
            ->with(['route.offer'])
            ->whereIn('id', $request->input('assignments'))
            ->get()
            ->each
            ->markAsLeftover();

        return back()->with('message', trans('crm::lead.mass_leftover_success'));
    }
}
