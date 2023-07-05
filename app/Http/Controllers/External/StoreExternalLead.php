<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\Create;
use App\Jobs\AssignMarkersToLead;
use App\Jobs\ProcessLead;
use App\Jobs\SimulateAutologin;
use App\LeadAssigner\LeadAssigner;

class StoreExternalLead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Leads\Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Create $request): \Illuminate\Http\JsonResponse
    {
        $lead = ProcessLead::dispatchNow($request->validated());

        if ($lead === null) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'general' => [
                        'ClickID or phone number was previously sent by you. Submission rejected'
                    ]
                ]
            ], 422);
        }

        if ($lead->affiliate_id === null) {
            AssignMarkersToLead::dispatchNow($lead);
            LeadAssigner::dispatchNow($lead);
        }


        try {
            if ($lead->offer_id === 1607 && ($assignment = $lead->assignments()->first()) !== null) {
                SimulateAutologin::dispatch($assignment)->onQueue('autologin');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()
            ->json([
                'message' => "Stored",
                'lead'    => [
                    'id'    => $lead->uuid,
                    'name'  => $lead->fullname,
                    'phone' => $lead->formatted_phone,
                    'valid' => $lead->valid,
                ]
            ], 201)->withHeaders(
                [
                    'Access-Control-Allow-Origin'  => '*',
                    'Access-Control-Allow-Methods' => 'POST,OPTIONS',
                    'Access-Control-Allow-Headers' => 'access-control-allow-headers, content-type, x-content-type-options, x-requested-with',
                    'Accept'                       => 'application/json',
                    'Content-Type'                 => 'application/json; charset=UTF-8;',
                    'X-Content-Type-Options'       => 'nosniff',
                ]
            );
    }
}
