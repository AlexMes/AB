<?php

namespace App\Services\Pourer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Pourer\Jobs\HandleCampaigns;
use Illuminate\Http\Request;

class Campaigns extends Controller
{
    public function __invoke(Request $request)
    {
        HandleCampaigns::dispatchNow($request->all());

        return response()->json(['message' => 'Accepted'], 202);
    }
}
