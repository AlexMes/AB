<?php

namespace App\Http\Controllers;

use App\Proxy;
use Illuminate\Http\Request;

class ProxyCheckController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Proxy                    $proxy
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, Proxy $proxy)
    {
        $this->authorize('view', $proxy);

        return response()->json($proxy->checkConnection($request->server('SERVER_ADDR')));
    }
}
