<?php

namespace App\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\AssignMarkersToLead;
use App\Lead;
use App\Leads\Http\Request\Registration;
use App\Leads\Pipes\CheckForDuplicates;
use App\Leads\Pipes\DetermineAffiliate;
use App\Leads\Pipes\DetermineLanding;
use App\Leads\Pipes\DetermineOffer;
use App\Leads\Pipes\FilterPhoneNumber;
use App\Leads\Pipes\FormatEmailAddress;
use App\Leads\Pipes\FormatRussianNumbers;
use App\Leads\Pipes\GenerateEmail;
use App\Leads\Pipes\GenerateUuid;
use App\Leads\Pipes\ReplaceIpForOffer;
use App\Leads\Pipes\SaveIntoDatabase;
use App\Leads\Pipes\ValidateName;
use App\Leads\SendLeadToCustomer;
use Illuminate\Pipeline\Pipeline;

class RegisterLead extends Controller
{
    /**
     * Steps of lead processing pipeline
     *
     * @var array|string[]
     */
    protected array $pipes = [
        GenerateUuid::class,
        DetermineOffer::class,
        DetermineAffiliate::class,
        DetermineLanding::class,
        ReplaceIpForOffer::class,
        ValidateName::class,
        FilterPhoneNumber::class,
        FormatRussianNumbers::class,
        GenerateEmail::class,
        FormatEmailAddress::class,
        CheckForDuplicates::class,
        SaveIntoDatabase::class,
    ];

    /**
     * CORS headers for response
     *
     * @var array|string[]
     */
    protected array $corsHeaders = [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Methods' => 'POST,OPTIONS',
        'Access-Control-Allow-Headers' => 'access-control-allow-headers, content-type, x-content-type-options, x-requested-with',
        'Accept'                       => 'application/json',
        'Content-Type'                 => 'application/json; charset=UTF-8;',
        'X-Content-Type-Options'       => 'nosniff',
    ];

    /**
     * Register and process lead
     *
     * @param \App\Leads\Http\Request\Registration $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Registration $request): \Illuminate\Http\JsonResponse
    {
        $lead = app(Pipeline::class)
            ->send(new Lead(array_merge($request->validated(), ['requestData' => $request->all()])))
            ->through($this->pipes)
            ->thenReturn();

        AssignMarkersToLead::dispatchNow($lead);

        $start = microtime(true);
        /** @var \App\LeadOrderAssignment $assignment */
        $assignment = SendLeadToCustomer::dispatchNow($lead);
        $end        = microtime(true);

        $assignment = optional($assignment)->fresh();
        $response   = [
            'message'  => 'Stored',
            'redirect' => optional(optional($assignment)->destination)->land_autologin
                ? optional($assignment)->redirect_url ?? null
                : null,
            'lead'     => [
                'id'    => $lead->uuid,
                'name'  => $lead->fullname,
                'phone' => $lead->formatted_phone,
                'valid' => $lead->valid,
            ],
        ];

        $lead->addEvent('response', array_merge($response, [
            'start' => $start,
            'end'   => $end,
            'diff'  => $end - $start,
        ]));

        return response()->json($response, 201)->withHeaders($this->corsHeaders);
    }
}
