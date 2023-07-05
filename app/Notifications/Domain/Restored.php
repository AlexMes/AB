<?php

namespace App\Notifications\Domain;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Domain;
use App\Notifications\NotificationSeverity;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Restored extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Domain that gone up
     *
     * @var \App\Domain
     */
    protected $domain;

    /**
     * Datetime of domain gone dead
     *
     * @var [type]
     */
    protected $since;

    /**
     * Create a new notification instance.
     *
     * @param \App\Domain $domain
     */
    public function __construct(Domain $domain, Carbon $since)
    {
        $this->domain = $domain;
        $this->since  = $since;
        $this->queue  = AdsBoard::QUEUE_NOTIFICATIONS;
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

    /**
     * Get telegram notification representation
     *
     * @return string
     */
    public function toTelegram()
    {
        return sprintf(
            "Домен %s снова онлайн. Был оффлайн %s",
            $this->domain->url,
            now()->longAbsoluteDiffForHumans($this->since)
        );
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
            'title'     => "Домен {$this->domain->url} снова онлайн.",
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
}
