<?php

namespace App\Notifications\Orders;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Created extends Notification implements ShouldQueue
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
     * @param \App\Events\Orders\Created $event
     *
     * @return void
     */
    public function handle(\App\Events\Orders\Created $event)
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
        return sprintf("Новый заказ доменов #%d", $this->order->id) . PHP_EOL
            . sprintf('*Ссылок*: %s', $this->order->links_count) . PHP_EOL
            . sprintf('*CloudFlare*: %s', $this->order->useCloudflare ? '✅' : '❌') . PHP_EOL
            . sprintf('*Конструктор*: %s', $this->order->useConstructor ? '✅' : '❌') . PHP_EOL
            . sprintf('*Клоака*: %s', optional($this->order->cloak)->name) . PHP_EOL
            . sprintf('*Связка*: %s', $this->order->binom_id) . PHP_EOL
            . sprintf('*Тип ссылок*: %s', $this->order->linkType) . PHP_EOL
            . sprintf('*Регистратор*: %s', optional($this->order->registrar)->name) . PHP_EOL
            . sprintf('*Хостинг*: %s', optional($this->order->hosting)->name) . PHP_EOL
            . sprintf('*Лендинг*: %s', $this->order->landing_id) . PHP_EOL
            . sprintf('*Дедлайн*: %s', $this->order->deadline_at) . PHP_EOL
            . PHP_EOL
            . url("/domains/orders/{$this->order->id}");
    }
}
