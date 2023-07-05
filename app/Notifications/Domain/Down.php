<?php

namespace App\Notifications\Domain;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Domain;
use App\Notifications\NotificationSeverity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Down extends Notification implements ShouldQueue
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
        return [TelegramChannel::class];
    }

    /**
     * Notification representation for Telegram
     *
     * @return string
     */
    public function toTelegram()
    {
        return "Домен {$this->domain->url} недоступен!".$this->latestLeadTs();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function pings()
    {
        return '@vladi_slav_2020, @anthonytonite, @YoIHaveAProblem_bot';
    }

    /**
     * Get timestamp for latest lead from
     * this domain
     *
     * @return void
     */
    protected function latestLeadTs()
    {
        $latestLead = $this->domain->leads()->latest()->max('created_at');

        if ($latestLead) {
            return 'Последний лид '.$latestLead;
        }

        return '';
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
