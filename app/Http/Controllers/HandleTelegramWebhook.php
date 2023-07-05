<?php

namespace App\Http\Controllers;

use App\Bot\Telegram;
use App\ManualApp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HandleTelegramWebhook extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if (Str::startsWith($request->input('message.text'), '/add_app ')) {
            $link   = trim(Str::replaceFirst('/add_app ', '', $request->input('message.text')));
            $link   = trim($link, '/');
            $chatId = $request->input('message.chat.id');

            $app = ManualApp::firstOrCreate(['link' => $link], ['chat_id' => $chatId, 'status' => ManualApp::NEW]);

            if (!$app->wasRecentlyCreated) {
                $message = sprintf("*Приложение уже существует.*");
                if (!in_array($chatId, explode(',', $app->chat_id))) {
                    $message .= sprintf("\n‼️ID этого чата (%s) отсутствует для уведомлений.", $chatId);
                }

                app(Telegram::class)
                    ->to($chatId)
                    ->say($message)
                    ->send();
            }
        }

        return response()->noContent();
    }
}
