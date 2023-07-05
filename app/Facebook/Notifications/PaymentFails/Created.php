<?php

namespace App\Facebook\Notifications\PaymentFails;

use App\AdsBoard;
use App\Notifications\NotificationSeverity;
use App\NotificationType;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Created extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Facebook\PaymentFail
     */
    protected $paymentFail;

    /**
     * Create a new notification instance.
     *
     * @param \App\Facebook\Events\PaymentFails\Created $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\PaymentFails\Created $event)
    {
        $this->paymentFail = $event->paymentFail;
        $this->queue       = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->paymentFail->user, $this);
        }

        $financiers = User::where('role', User::FINANCIER)->where('id', '!=', $event->paymentFail->user_id)->get();
        \Illuminate\Support\Facades\Notification::send($financiers, $this);
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
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'    => sprintf(
                'Сбой оплаты кабинета %s (%s). Карта: %s',
                $this->paymentFail->account->getName(),
                $this->paymentFail->account_id,
                $this->paymentFail->card
            ),
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
     * Determine when notification should really be sent
     *
     * @return bool
     */
    protected function shouldSend()
    {
        return $this->paymentFail->user->hasTelegramNotification(NotificationType::PAYMENT_FAIL_CREATED);
    }
}
