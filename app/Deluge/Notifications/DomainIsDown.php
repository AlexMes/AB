<?php

namespace App\Deluge\Notifications;

use App\AdsBoard;
use App\Deluge\Domain;
use App\Notifications\NotificationSeverity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DomainIsDown extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Domain model
     *
     * @var \App\Domain
     */
    protected $domain;

    /**
     * Create a new notification instance.
     *
     * @param \App\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
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
        return ['broadcast'];
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
            'title'     => "Домен {$this->domain->url} недоступен!",
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
}
