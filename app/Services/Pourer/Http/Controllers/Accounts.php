<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandleAccounts;
use Illuminate\Http\Request;

class Accounts extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        HandleAccounts::dispatchNow($request->profile, $request->accounts);

        return response()->json(['message' => 'Accepted'], 202);
    }
}
