<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkLeadLeftover;
use App\Lead;

class MarkLeadLeftoverController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Lead $lead, MarkLeadLeftover $request)
    {
        $lead->toLeftover();
        if ($request->boolean('delete_assignments')) {
            $lead->assignments()->get()->each->remove();
        }

        return response()->json($lead->loadMissing(['user','account'])->load('offer'));
    }
}
