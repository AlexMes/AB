<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandlePages;
use Illuminate\Http\Request;

class Pages extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        HandlePages::dispatchNow($request->profile, $request->pages);

        return response()->json(['message' => 'Accepted'], 202);
    }
}
