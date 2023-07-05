<?php

namespace App\Notifications\Orders;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

class Updated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Order instance
     *
     * @var \App\Order
     */
    protected $order;

    /**
     * Labels for model attributes
     *
     * @var array
     */
    protected $labels = [
        'links_count'    => 'Ссылок',
        'useCloudflare'  => 'CloudFlare',
        'useConstructor' => 'Конструктор',
        'cloak'          => 'Клоака',
        'linkType'       => 'Тип ссылок',
        'binom_id'       => 'Связка',
        'registrar'      => 'Регистратор',
        'landing_id'     => 'Ленд',
        'deadline_at'    => 'Дедлайн',
    ];

    /**
     * Create a new notification instance.
     *
     * @param \App\Events\Orders\Updated $event
     *
     * @return void
     */
    public function handle(\App\Events\Orders\Updated $event)
    {
        $this->order = $event->order;
        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send(AdsBoard::devsChannel(), $this);
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
        return [TelegramChannel::class];
    }

    /**
     * Get message contents
     *
     * @return string
     */
    public function toTelegram()
    {
        return sprintf("Обновлен заказ #%d", $this->order->id) . PHP_EOL
            . $this->changes()
            . PHP_EOL
            . url("/domains/orders/{$this->order->id}");
    }

    /**
     * Determine when notification should really be sent
     *
     * @return bool
     */
    protected function shouldSend()
    {
        return Arr::except($this->order->getDirty(), ['links_done_count','updated_at']) !== [];
    }

    /**
     * Get changes in order
     *
     * @return string
     */
    protected function changes()
    {
        $message = '';

        foreach ($this->order->getChanges() as $key => $change) {
            if (in_array($key, ['useCloudflare','useConstructor'])) {
                $message .= sprintf(
                    "*%s*: %s => %s ",
                    $this->labels[$key] ?? $key,
                    $this->order->getOriginal($key) ? '✅' : '❌',
                    $change ? '✅' : '❌'
                ) . PHP_EOL;
            }

            $message .= sprintf(
                "*%s*: %s => %s ",
                $this->labels[$key] ?? $key,
                $this->order->getRawOriginal($key),
                $change
            ) . PHP_EOL;
        }

        return $message;
    }
}
