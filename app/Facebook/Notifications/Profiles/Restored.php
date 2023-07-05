<?php

namespace App\Facebook\Notifications\Profiles;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Notifications\NotificationSeverity;
use App\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Restored extends Notification
{
    use Queueable;

    /**
     * Order instance
     *
     * @var \App\Facebook\Profile
     */
    protected $profile;

    /**
     * Create a new notification instance
     *
     * @param \App\Facebook\Events\Profiles\Restored $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\Profiles\Restored $event)
    {
        $this->profile = $event->profile;

        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->profile->user, $this);
        }
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
        $result = ['database', 'broadcast'];
        if ($notifiable->telegram_id) {
            $result[] = TelegramChannel::class;
        }

        return $result;
    }

    /**
     * Get message contents
     *
     * @return string
     */
    public function toTelegram()
    {
        return "Профиль *#{$this->profile->id} {$this->profile->name}*. восстановлен" ;
    }

    /**
     * Array representation for the notification
     *
     * @param $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'     => "Профиль *#{$this->profile->id} {$this->profile->name}*. восстановлен",
            'body'      => null,
            'severity'  => NotificationSeverity::INFO,
        ];
    }

    /**
     * Notification representation for broadcasting
     *
     * @param $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Determine when notification should really be sent
     *
     * @return bool
     */
    protected function shouldSend()
    {
        return optional(optional($this->profile)->user)->hasTelegramNotification(NotificationType::PROFILE_RESTORED);
    }
}
