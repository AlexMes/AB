<?php

namespace App\Http\Controllers;

use App\Jobs\Leads\HandleBitrixUpdateHook;
use App\LeadOrderAssignment;
use App\Services\Bitrix24\Bitrix24;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceiveIncomingLead extends Controller
{
    /**
     * Handle B24 calls here
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Log::channel('b24')->info(json_encode($request->all()));

        $assignment = LeadOrderAssignment::whereExternalId($request->get('data')['FIELDS']['ID'])->first();

        if ($request->get('event') === Bitrix24::LEAD_UPDATE_HOOK && !is_null($assignment)) {
            HandleBitrixUpdateHook::dispatch($assignment);
        }

        return response('Accepted', 202);
    }
}
