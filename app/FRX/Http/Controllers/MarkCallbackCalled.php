<?php

namespace App\FRX\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\FRX\Http\Requests\MarkCallbackCalled as Request;
use App\Http\Controllers\Controller;

class MarkCallbackCalled extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request             $request
     * @param LeadOrderAssignment $frxAssignment
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, LeadOrderAssignment $frxAssignment)
    {
        $callback = $frxAssignment->actualCallback();

        $result = $callback->update([
            'called_at'   => $request->input('called_at', now()->toDateTimeString()),
            'frx_call_id' => $request->input('call_id', $callback->frx_call_id),
        ]);

        abort_unless($result, 404, 'No actual callback found. A callback should be created first.');

        return response()->noContent(202);
    }
}
