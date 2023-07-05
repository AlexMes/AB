<?php

namespace App\Integrations\Http\Postbacks;

use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;

class AivixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function __invoke(Request $request)
    {
        if (!$request->lead_id || !$request->status) {
            return abort(403);
        }

        $lead = Lead::findOrFail($request->lead_id);

        if ($lead->lead_status) {
            $lead->update(['status' => $lead->lead_status]);
        }

        if ($request->sum) {
            $lead->deposits()->create([
                'phone'            => $lead->phone,
                'account_id'       => $lead->account_id,
                'offer_id'         => $lead->offer_id,
                'user_id'          => $lead->user_id,
                'office_id'        => $lead->office_id,
                'sum'              => intval($request->sum),
                'date'             => now(),
                'lead_return_date' => $lead->created_at,
            ]);
        }

        return response()->json([
            'message' => "Stored"
        ], 201);
    }
}
