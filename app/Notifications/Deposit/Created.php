<?php

namespace App\Notifications\Deposit;

use App\Bot\TelegramChannel;
use App\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Created extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Deposit
     */
    protected Deposit $deposit;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
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

    public function toTelegram()
    {
        $assignment = $this->deposit->getAssignment();

        $message = "*Новый депозит*💰\n";
        $message .= "Лид: " . $this->deposit->lead_id . "\n";
        if ($assignment !== null) {
            $message .= sprintf(
                "Доставка: %s(%s), %s\n",
                $assignment->destination->name,
                $assignment->destination->id,
                $assignment->destination->driver
            );
        }

        return $message;
    }
}
