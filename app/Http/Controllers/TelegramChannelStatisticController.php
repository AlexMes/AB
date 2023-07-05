<?php

namespace App\Http\Controllers;

use App\Http\Requests\Telegram\CreateStats;
use App\Http\Requests\Telegram\UpdateStats;
use App\TelegramChannel;
use App\TelegramChannelStatistic;

class TelegramChannelStatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return TelegramChannelStatistic::with('channel')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\TelegramChannel                    $channel
     * @param \App\Http\Requests\Telegram\CreateStats $request
     *
     * @return \App\TelegramChannelStatistic|\Illuminate\Database\Eloquent\Model
     */
    public function store(TelegramChannel $channel, CreateStats $request)
    {
        return $channel->statistics()->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Telegram\UpdateStats $request
     * @param \App\TelegramChannel                    $channel
     * @param \App\TelegramChannelStatistic           $telegramChannelStatistic
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        UpdateStats $request,
        TelegramChannel $channel,
        TelegramChannelStatistic $telegramChannelStatistic
    ) {
        return response()->json(
            tap($telegramChannelStatistic)->update($request->validated()),
            202
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\TelegramChannelStatistic $telegramChannelStatistic
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TelegramChannelStatistic $telegramChannelStatistic)
    {
        $telegramChannelStatistic->delete();

        return response()->noContent();
    }
}
