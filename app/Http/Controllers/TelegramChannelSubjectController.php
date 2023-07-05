<?php

namespace App\Http\Controllers;

use App\Http\Requests\Telegram\CreateSubject;
use App\Http\Requests\Telegram\UpdateSubject;
use App\TelegramChannelSubject;

class TelegramChannelSubjectController extends Controller
{
    /**
     * Load channel subjects
     *
     * @return \App\TelegramChannelSubject[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return TelegramChannelSubject::all();
    }

    /**
     * Create channel subject
     *
     * @param \App\Http\Requests\Telegram\CreateSubject $request
     *
     * @return mixed
     */
    public function store(CreateSubject $request)
    {
        return TelegramChannelSubject::create($request->validated());
    }

    /**
     * Update channel subjects
     *
     * @param \App\TelegramChannelSubject               $subject
     * @param \App\Http\Requests\Telegram\UpdateSubject $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TelegramChannelSubject $subject, UpdateSubject $request)
    {
        return response()->json(
            tap($subject)->update($request->validated()),
            202
        );
    }
}
