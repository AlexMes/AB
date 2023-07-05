<?php

namespace App\Facebook\Notifications\Accounts;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Notifications\NotificationSeverity;
use App\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Updated extends Notification
{
    use Queueable;

    /**
     * Account instance
     *
     * @var \App\Facebook\Account
     */
    protected $account;

    /**
     * Create a new notification instance.
     *
     * @param \App\Facebook\Events\Accounts\Updated $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\Accounts\Updated $event)
    {
        $this->account = $event->account;
        $this->queue   = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->account->profile->user, $this);
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
        return "Статус кабинета {$this->account->getName()} обновлен до {$this->account->status}.";
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
            'title'     => "Статус кабинета {$this->account->getName()} обновлен до {$this->account->status}.",
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
        return array_key_exists('account_status', $this->account->getDirty())
            && $this->account->profile->user->hasTelegramNotification(NotificationType::ACCOUNT_STATUS_UPDATED);
    }
}
