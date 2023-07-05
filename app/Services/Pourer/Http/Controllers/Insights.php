<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandleInsights;
use Illuminate\Http\Request;

class Insights extends Controller
{
    public function __invoke(Request $request)
    {
        \Log::info(json_encode($request->all()));
        HandleInsights::dispatchNow($request->all());

        return response()->json(['message' => 'Accepted'], 202);
    }
}
