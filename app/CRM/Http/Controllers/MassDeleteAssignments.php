<?php

namespace App\CRM\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MassDeleteAssignments extends Controller
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
        $this->authorize('massDelete', LeadOrderAssignment::class);

        LeadOrderAssignment::query()
            ->with(['lead', 'route.order'])
            ->whereIn('id', $request->input('assignments'))
            ->get()
            ->each
            ->remove();

        return back()->with('message', trans('crm::lead.mass_delete_success'));
    }
}
