<?php

namespace App\Bot;

use Illuminate\Notifications\Notification;

class TelegramChannel
{
    /**
     * Telegram service instance
     *
     * @var \App\Bot\Telegram
     */
    protected $telegram;

    /**
     * TelegramChannel constructor.
     *
     * @param \App\Bot\Telegram $telegram
     *
     * @return void
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Send given notification
     *
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $this->telegram->say($notification->toTelegram())
            ->to(is_object($notifiable) ? $notifiable->telegram_id : $notifiable)
            ->send();
    }
}
