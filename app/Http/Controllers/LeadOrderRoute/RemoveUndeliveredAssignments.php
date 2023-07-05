<?php

namespace App\Http\Controllers\LeadOrderRoute;

use App\Http\Controllers\Controller;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Http\Request;

class RemoveUndeliveredAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, Request $request)
    {
        $this->authorize('update', $route);

        $route->assignments()
            ->undelivered()
            ->each(fn (LeadOrderAssignment $assignment) => $assignment->remove());

        return response()->noContent();
    }
}
