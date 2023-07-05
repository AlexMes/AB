<?php

namespace App\Integrations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Payload;
use Illuminate\Http\Request;

class PayloadsController extends Controller
{
    /**
     * Load Payloads.
     *
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->authorize('index', Payload::class);

        return Payload::notEmptyWhere('form_id', $request->form_id)
            ->notEmptyWhere('offer_id', $request->offer_id)
            ->orderBy('created_at', 'desc')
            ->with('lead', 'offer', 'form', 'landing')
            ->paginate();
    }

    /**
     * @param Payload $payload
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return Payload
     */
    public function show(Payload $payload)
    {
        $this->authorize('show', $payload);

        return $payload->load('lead', 'offer', 'form');
    }
}
