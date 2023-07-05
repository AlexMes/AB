<?php

namespace App\Notifications\Reports;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Metrics\Traffic\Cost;
use App\Metrics\Traffic\CPL;
use App\Metrics\Traffic\CPM;
use App\Metrics\Traffic\CTR;
use App\Metrics\Traffic\LeadsCount;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class Statistic extends Notification
{
    use Queueable;

    /**
     * Order instance
     *
     * @var \App\Order
     */
    protected $order;

    /**
     * Date for report generation
     *
     * @var \Illuminate\Support\Carbon|null
     */
    protected $date;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Support\Carbon|null $date
     */
    public function __construct(Carbon $date = null)
    {
        $this->queue = AdsBoard::QUEUE_NOTIFICATIONS;
        $this->date  = $date ?? now();
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
     * @param mixed $notifiable
     *
     * @return string
     */
    public function toTelegram($notifiable)
    {
        $message = '';

        foreach ($this->toArray($notifiable) as $item) {
            $message .= sprintf('*Offer*: %s', $item['offer']) . PHP_EOL .
            sprintf('*Cost*: %s', $item['cost']) . PHP_EOL .
            sprintf('*Leads*: %s', $item['leads']) . PHP_EOL .
            sprintf('*CPL*: %s', $item['cpl']) . PHP_EOL .
            sprintf('*CPM*:  %s', $item['cpm']) . PHP_EOL .
            sprintf('*CTR*: %s', $item['ctr']) . PHP_EOL;
        }

        return $message;
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
        return Offer::all()->map(function (Offer $offer) {
            return [
                'offer' => $offer->name,
                'cost'  => Cost::make()->forDate($this->date)->forOffers($offer),
                'leads' => LeadsCount::make()->forDate($this->date)->forOffers($offer),
                'cpl'   => CPL::make()->forDate($this->date)->forOffers($offer),
                'cpm'   => CPM::make()->forDate($this->date)->forOffers($offer),
                'ctr'   => CTR::make()->forDate($this->date)->forOffers($offer),
            ];
        })->toArray();
    }
}
