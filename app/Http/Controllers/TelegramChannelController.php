<?php

namespace App\Http\Controllers;

use App\Http\Requests\Telegram\CreateChannel;
use App\Http\Requests\Telegram\UpdateChannel;
use App\TelegramChannel;
use Illuminate\Http\Request;

class TelegramChannelController extends Controller
{
    /**
     * TelegramChannelController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(TelegramChannel::class, 'channel');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return TelegramChannel::with('subject')
            ->when($request->has('all'), fn ($query) => $query->get())
            ->unless($request->has('all'), fn ($query) => $query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Telegram\CreateChannel $request
     *
     * @return \App\TelegramChannel|\Illuminate\Database\Eloquent\Model
     */
    public function store(CreateChannel $request)
    {
        return TelegramChannel::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TelegramChannel $channel
     *
     * @return \App\TelegramChannel
     */
    public function show(TelegramChannel $channel)
    {
        return $channel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Telegram\UpdateChannel $request
     * @param \App\TelegramChannel                      $channel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateChannel $request, TelegramChannel $channel)
    {
        return response()->json(tap($channel)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\TelegramChannel $channel
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TelegramChannel $channel)
    {
        $channel->delete();

        return response()->noContent();
    }
}
