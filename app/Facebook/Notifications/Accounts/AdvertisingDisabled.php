<?php

namespace App\Facebook\Notifications\Accounts;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Facebook\Account;
use App\Notifications\NotificationSeverity;
use App\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdvertisingDisabled extends Notification
{
    use Queueable;

    /**
     * @var Account
     */
    protected Account $account;

    /**
     * @param \App\Facebook\Events\Accounts\Updated $event
     */
    public function handle(\App\Facebook\Events\Accounts\Updated $event)
    {
        $this->account = $event->account;
        $this->queue   = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($this->account->user, $this);
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
     * @return string
     */
    public function toTelegram()
    {
        return "Рекламная функци кабинета {$this->account->getName()} отлючена";
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
            'title'     => "Рекламная функци кабинета {$this->account->getName()} отлючена",
            'body'      => null,
            'severity'  => NotificationSeverity::WARNING,
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
     * Determines whether notification should be sent
     *
     * @return bool
     */
    protected function shouldSend()
    {
        return $this->account->isDirty('ad_disabled_at')
            && $this->account->ad_disabled_at !== null
            && $this->account->user->hasTelegramNotification(NotificationType::ACCOUNT_ADVERTISING_DISABLED);
    }
}
