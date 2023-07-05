<?php

namespace App\Http\Controllers;

use App\Http\Requests\Telegram\BulkStatsCreate;
use App\Jobs\Telegram\SaveChannelStatistic;
use Illuminate\Http\Request;

class StoreTelegramChannelsBulkStatistics extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Telegram\BulkStatsCreate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(BulkStatsCreate $request)
    {
        SaveChannelStatistic::dispatchNow($request->validated());

        return response()->json(['message' => 'Saved'], 201);
    }
}
