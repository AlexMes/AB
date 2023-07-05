<?php

namespace App\Http\Controllers;

use App\LeadOrderAssignment;

class DestroyOrderRouteAssignment extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LeadOrderAssignment $assignment
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function __invoke(LeadOrderAssignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->remove();

        return response()->noContent();
    }
}
