<?php

namespace App\Http\Controllers;

use App\Bot\Telegram;
use App\Branch;
use App\Http\Requests\Branches\SendTgMessage;

class SendBranchTgMessageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Branch        $branch
     * @param SendTgMessage $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Branch $branch, SendTgMessage $request)
    {
        app(Telegram::class)
            ->to($branch->telegram_id)
            ->say($request->input('message'))
            ->send();

        return response()->noContent();
    }
}
