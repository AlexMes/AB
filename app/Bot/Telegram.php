<?php

namespace App\Bot;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zttp\Zttp;

class Telegram
{
    /**
     * Telegram's bot API token
     *
     * @var string
     */
    protected $token;

    /**
     * Collection of message recipients
     *
     * @var \Illuminate\Support\Collection
     */
    protected $recipients;

    /**
     * Message to send
     *
     * @var string
     */
    protected $message;

    /**
     * Telegram constructor.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Set notification recipients
     *
     * @param string|array $recipients
     *
     * @return \App\Bot\Telegram
     */
    public function to($recipients)
    {
        $this->recipients = collect(Arr::wrap($recipients));

        return $this;
    }

    /**
     * Set notification message
     *
     * @param string $message
     *
     * @return \App\Bot\Telegram
     */
    public function say($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Actually send notification
     *
     * @return void
     */
    public function send()
    {
        $this->recipients->each(function ($recipient) {
            try {
                Http::post(sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token), [
                    'chat_id'                  => $recipient,
                    'parse_mode'               => 'Markdown',
                    'text'                     => $this->message,
                    'disable_web_page_preview' => true,
                ]);
            } catch (\Throwable $exception) {
                Log::error('Notification failure');
            }
        });
    }

    /**
     * Configure webhook
     *
     * @return bool
     */
    public function register()
    {
        return Zttp::post(
            sprintf('https://api.telegram.org/bot%s/setWebhook', $this->token),
            [
                'url'             => route('telegram.webhook'),
                'max_connections' => config('bot.telegram.max_connections')
            ]
        )->isOk();
    }

    /**
     * Delete webhook
     *
     * @return bool
     */
    public function unregister()
    {
        return Zttp::post(
            sprintf('https://api.telegram.org/bot%s/setWebhook', $this->token),
            [
                'url' => null,
            ]
        )->isOk();
    }

    public function status()
    {
        return [
            'info'    => Zttp::get(sprintf('https://api.telegram.org/bot%s/getMe', $this->token))->json(),
            'webhook' => Zttp::get(sprintf('https://api.telegram.org/bot%s/getWebhookInfo', $this->token))->json(),
        ];
    }
}
