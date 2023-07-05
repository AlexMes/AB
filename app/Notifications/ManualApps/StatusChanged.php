<?php

namespace App\Notifications\ManualApps;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\ManualApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ManualApp
     */
    protected ManualApp $app;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ManualApp $app)
    {
        $this->app   = $app;
        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram()
    {
        $message = $this->app->status === ManualApp::NEW
            ? "☑️"
            : ($this->app->status === ManualApp::PUBLISHED ? "✅" : "❌");
        $message .= ' Приложение ' .
            $this->app->link .
            ($this->app->status === ManualApp::NEW
                ? " добавлено в Google Play."
                : ($this->app->status === ManualApp::PUBLISHED ? " опубликовано в Google Play." : " удалено из Google Play."));

        return $message;
    }
}
