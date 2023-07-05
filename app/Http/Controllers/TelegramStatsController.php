<?php

namespace App\Http\Controllers;

use App\TelegramChannelStatistic;
use Illuminate\Http\Request;

class TelegramStatsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return TelegramChannelStatistic::query()
            ->with('channel')
            ->orderByDesc('date')
            ->paginate();
    }
}
