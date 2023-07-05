<?php

namespace App\Facebook\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisconnectProfile extends Controller
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
        \App\Facebook\Jobs\DisconnectProfile::dispatch($request->input('signed_request'));

        return response()->noContent();
    }
}
