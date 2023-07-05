<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;

class LeadRestoreController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param mixed                    $leadId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke($leadId, Request $request)
    {
        $lead = Lead::onlyTrashed()->findOrFail($leadId);

        $this->authorize('restore', $lead);

        $lead->recordAs(Lead::RESTORED)->restore();

        return response()->json($lead->loadMissing(['user','offer','account']));
    }
}
