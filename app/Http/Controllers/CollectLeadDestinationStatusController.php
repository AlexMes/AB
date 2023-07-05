<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadsDestinations\CollectStatuses;
use App\Jobs\CollectLeadDestinationStatuses;
use App\LeadDestination;

class CollectLeadDestinationStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CollectStatuses $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadDestination $leadsDestination, CollectStatuses $request)
    {
        CollectLeadDestinationStatuses::dispatchNow($leadsDestination);

        return response()->noContent();
    }
}
