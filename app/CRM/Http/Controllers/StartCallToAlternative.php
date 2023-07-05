<?php

namespace App\CRM\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class StartCallToAlternative extends Controller
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

        if (! $assignment->hasAltPhone()) {
            Session::flash('message', 'Нет альтернативного номера для набора');
        }

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', auth('crm')->user()->frx_access_token)
        ])->post(sprintf('%s/api/calls/dial', optional(auth('crm')->user()->tenant)->url), [
            'number' => $assignment->formatted_alt_phone,
        ]);

        if (array_key_exists('call_id', $response->json())) {
            $assignment->lead->addEvent(Lead::CALLED_ALT, [
                'called_at' => now(),
                'call_id'   => $response->offsetGet('call_id'),
            ]);

            $assignment->actualCallback()->update([
                'called_at'   => now()->toDateTimeString(),
                'frx_call_id' => $response->offsetGet('call_id')
            ]);
        }

        Session::flash('message', $response->offsetGet('message') ?? 'Failure');

        return redirect()->route('crm.assignments.show', $assignment);
    }
}
