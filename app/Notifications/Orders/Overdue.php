<?php

namespace App\Notifications\Orders;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class Overdue extends Notification
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
     * @param \App\Order $order
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;
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
        return sprintf("Истек срок выполнение заказа #%d", $this->order->id) . PHP_EOL
            . sprintf('*Ссылок*: %s', $this->order->links_done_count) . PHP_EOL
            . sprintf('*Должно быть ссылок*: %s', $this->order->links_count) . PHP_EOL
            . sprintf('*Готовность*: %d%%', $this->orderCompletePercentage()) . PHP_EOL
            . sprintf('*Дедлайн*: %s', $this->order->deadline_at) . PHP_EOL
            . PHP_EOL
            . url("/domains/orders/{$this->order->id}");
    }

    /**
     * Get order progress in percentage
     *
     * @return int
     */
    protected function orderCompletePercentage()
    {
        if ($this->order->links_count === 0) {
            return 0;
        }

        return ($this->order->links_done_count / $this->order->links_count) * 100;
    }
}
