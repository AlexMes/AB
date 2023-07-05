<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandleAdSets;
use Illuminate\Http\Request;

class AdSets extends Controller
{
    public function __invoke(Request $request)
    {
        HandleAdSets::dispatchNow($request->all());

        return response()->json(['message' => 'Accepted'], 202);
    }
}
