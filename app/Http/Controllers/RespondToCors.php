<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RespondToCors extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        return response()
            ->json(['message' => 'Allowed'], 200)
            ->withHeaders([
                'Access-Control-Allow-Origin'  => '*',
                'Access-Control-Allow-Methods' => 'POST,GET,OPTIONS',
                'Access-Control-Allow-Headers' => 'access-control-allow-headers, content-type, x-content-type-options, x-requested-with',
                'Accept'                       => 'application/json; application/html',
                'Content-Type'                 => 'application/json; charset=UTF-8; application/html',
                'X-Content-Type-Options'       => 'nosniff',
            ])
           ;
    }
}
