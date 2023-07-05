<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarquizReceiveIncomingLead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = [
            'firstname' => $request->input('contacts.name'),
            'email'     => $request->input('contacts.email'),
            'phone'     => digits($request->input('contacts.phone')),
            'poll'      => $request->input('answers'),
            'ip'        => $request->input('extra.ip'),
            'offer'     => $request->input('offer'),
        ];
        if (empty($data['email'])) {
            unset($data['email']);
        }

        $response = Http::asJson()->acceptJson()->post('https://uleads.app/leads/register', $data);

        if ($response->failed()) {
            logger($response->body(), ['marquiz.ru']);
        }

        return response()->noContent();
    }
}
