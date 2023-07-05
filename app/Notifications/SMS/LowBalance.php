<?php

namespace App\Notifications\SMS;

use App\Bot\TelegramChannel;
use Illuminate\Notifications\Notification;

class LowBalance extends Notification
{
    /**
     * @var float|string
     */
    protected $balance;

    /**
     * @var string|null
     */
    protected ?string $currency;

    /**
     * @var int|null
     */
    protected ?string $branchId;

    /**
     * Create a new notification instance.
     *
     * @param float|string $balance
     * @param string|null  $currency
     * @param int|null     $branchId
     */
    public function __construct($balance, $currency = null, $branchId = null)
    {
        $this->balance  = $balance;
        $this->currency = $currency;
        $this->branchId = $branchId;
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
        return $notifiable ? [TelegramChannel::class] : null;
    }

    /**
     * Get the telegram representation of the notification.
     */
    public function toTelegram()
    {
        return sprintf('Низкий баланс на епочте(branch-%s): %s %s', $this->branchId, $this->balance, $this->currency);
    }
}
