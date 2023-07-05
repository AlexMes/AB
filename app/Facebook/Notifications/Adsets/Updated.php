<?php

namespace App\Facebook\Notifications\Adsets;

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
     * Adset instance
     *
     * @var \App\Facebook\AdSet
     */
    protected $adset;

    /**
     * Create a new notification instance.
     *
     * @param \App\Facebook\Events\Adsets\Updated $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\Adsets\Updated $event)
    {
        $this->adset   = $event->adset;
        $this->queue   = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->adset->account->profile->user, $this);
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
        if ($this->shouldSendWithIssue()) {
            return "Статус адсета {$this->adset->name} обновлен до {$this->adset->status}. Проблема: {$this->adset->issues_info[0]['error_summary']}.";
        }

        return "Статус адсета {$this->adset->name} обновлен до {$this->adset->status}.";
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
            'title'     => "Статус адсета {$this->adset->name} обновлен до {$this->adset->status}.",
            'body'      => $this->shouldSendWithIssue()
                ? "Статус адсета {$this->adset->name} обновлен до {$this->adset->status}. Проблема: {$this->adset->issues_info[0]['error_summary']}."
                : null,
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
        return array_key_exists('status', $this->adset->getDirty())
            && optional($this->adset->account->profile->user)->hasTelegramNotification(NotificationType::ADSET_STATUS_UPDATED);
    }

    /**
     * Determine when notification with issue should be sent
     *
     * @return bool
     */
    protected function shouldSendWithIssue()
    {
        return $this->adset->issues_info !== null;
    }
}
