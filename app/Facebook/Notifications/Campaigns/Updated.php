<?php

namespace App\Facebook\Notifications\Campaigns;

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
     * Campaign instance
     *
     * @var \App\Facebook\Campaign
     */
    protected $campaign;

    /**
     * Create a new notification instance.
     *
     * @param \App\Facebook\Events\Campaigns\CampaignUpdated $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\Campaigns\CampaignUpdated $event)
    {
        $this->campaign   = $event->campaign;
        $this->queue      = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->campaign->account->profile->user, $this);
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
        return "Статус кампании {$this->campaign->name} обновлен до {$this->campaign->status}.";
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
            'title'     => "Статус кампании {$this->campaign->name} обновлен до {$this->campaign->status}.",
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
        return array_key_exists('status', $this->campaign->getDirty())
            && $this->campaign->account->profile->user->hasTelegramNotification(NotificationType::CAMPAIGN_STATUS_UPDATED);
    }
}
