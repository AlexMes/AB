<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadsDestinations\Test as Request;
use App\Jobs\Leads\FetchIpAddressData;
use App\Jobs\LookupPhoneNumber;
use App\Lead;
use App\LeadDestination;
use Illuminate\Support\Str;

class TestLeadDestinationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param LeadDestination $leadsDestination
     * @param Request         $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadDestination $leadsDestination, Request $request)
    {
        $lead = Lead::makeForTestDestination($request->validated());

        $lead->saveQuietly();
        $lead->uuid = (string) Str::uuid();
        LookupPhoneNumber::dispatchNow($lead);
        FetchIpAddressData::dispatchNow($lead);

        // send lead
        $driver = $leadsDestination->initialize();
        $driver->send($lead);

        $leadsDestination->update([
            'test_payload' => [
                'email'    => $lead->email,
                'request'  => $request->validated(),
                'response' => [
                    'delivered'    => $driver->isDelivered(),
                    'external_id'  => $driver->getExternalId(),
                    'redirect_url' => $driver->getRedirectUrl(),
                    'error'        => $driver->getError(),
                ],
            ],
        ]);

        $lead->forceDelete();

        return response()->json($leadsDestination->loadMissing(['branch']));
    }
}
