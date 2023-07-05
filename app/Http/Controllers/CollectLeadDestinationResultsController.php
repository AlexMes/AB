<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadsDestinations\CollectResults;
use App\Jobs\PullResultsFromDestination;
use App\LeadDestination;

class CollectLeadDestinationResultsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param LeadDestination $leadsDestination
     * @param CollectResults  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadDestination $leadsDestination, CollectResults $request)
    {
        PullResultsFromDestination::dispatch($leadsDestination, $request->input('since'));

        return response()->noContent();
    }
}
