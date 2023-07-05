<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandleAds;
use Illuminate\Http\Request;

class Ads extends Controller
{
    public function __invoke(Request $request)
    {
        HandleAds::dispatchNow($request->all());

        return response()->json(['message' => 'Accepted'], 202);
    }
}
