<?php

namespace App\CRM\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class StartCall extends Controller
{
    /**
     * Start a new call on TCRM
     *
     * @param \App\CRM\LeadOrderAssignment $assignment
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function __invoke(LeadOrderAssignment $assignment)
    {
        $this->authorize('view', $assignment);

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', auth('crm')->user()->frx_access_token)
        ])->post(sprintf('%s/api/calls/dial', optional(auth('crm')->user()->tenant)->url), [
            'number' => $assignment->lead->formatted_phone,
        ]);

        if (array_key_exists('call_id', $response->json())) {
            DB::transaction(function () use ($assignment, $response) {
                $now = now();
                $assignment->lead->addEvent(
                    Lead::CALLED,
                    ['called_at'   => $now, 'frx_call_id' => $response->offsetGet('call_id')],
                    ['called_at'   => $assignment->called_at, 'frx_call_id' => $assignment->frx_call_id],
                );

                // this is used to understand when lead was
                // called after assignment
                if ($assignment->frx_call_id === null) {
                    $assignment->update([
                        'called_at'   => $now,
                        'frx_call_id' => $response->offsetGet('call_id'),
                    ]);
                }

                $assignment->actualCallback()->update([
                    'called_at'   => $now,
                    'frx_call_id' => $response->offsetGet('call_id')
                ]);
            });
        }

        Session::flash('message', $response->json()['message'] ?? 'Failure');

        return redirect()->back();
    }
}
