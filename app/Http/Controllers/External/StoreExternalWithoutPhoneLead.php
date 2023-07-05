<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\CreateWithoutPhone;
use App\Jobs\ProcessLead;
use App\LeadAssigner\LeadAssigner;

class StoreExternalWithoutPhoneLead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Leads\CreateWithoutPhone $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateWithoutPhone $request): \Illuminate\Http\JsonResponse
    {
        $lead = ProcessLead::dispatchNow($request->validated());

        if ($lead === null) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'general' => [
                        'ClickID or email was previously sent by you. Submission rejected'
                    ]
                ]
            ], 422);
        }

        LeadAssigner::dispatch($lead);

        return response()
            ->json([
                'message' => "Stored",
                'lead'    => [
                    'id'    => $lead->uuid,
                    'name'  => $lead->fullname,
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
