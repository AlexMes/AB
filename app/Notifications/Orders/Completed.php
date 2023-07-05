<?php

namespace App\Notifications\Orders;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Notification;

class Completed extends Notification
{
    use Queueable;

    /**
     * Order instance
     *
     * @var \App\Order
     */
    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param \App\Events\Orders\Completed $event
     *
     * @return void
     */
    public function handle(\App\Events\Orders\Completed $event)
    {
        $this->order = $event->order;
        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;

        app(Dispatcher::class)->send(AdsBoard::devsChannel(), $this);
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
     * Get message contents
     *
     * @return string
     */
    public function toTelegram()
    {
        $message = sprintf("Заказ #%d выполнен", $this->order->id) . PHP_EOL
                    . "*Домены:*" . PHP_EOL;

        foreach ($this->order->domains()->get() as $domain) {
            $message .= sprintf('*%d.*  %s', $domain->id, $domain->url) . PHP_EOL;
        }

        return $message . PHP_EOL . url("/domains/orders/{$this->order->id}");
    }
}
