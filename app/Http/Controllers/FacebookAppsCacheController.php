<?php

namespace App\Http\Controllers;

use App\Facebook\FacebookApp;
use Illuminate\Http\Request;

class FacebookAppsCacheController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param FacebookApp              $facebookApp
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(FacebookApp $facebookApp, Request $request)
    {
        $this->authorizeResource('update', $facebookApp);

        if (FacebookApp::forget($facebookApp->id)) {
            return response(['message' => 'cache dropped'], 200);
        }

        return response(['message' => 'cache not dropped'], 400);
    }
}
