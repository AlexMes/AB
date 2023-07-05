<?php

namespace App\Notifications;

use App\Bot\TelegramChannel;
use App\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Class SubscriptionPaymentsDue
 *
 * This fucking crap is great to teach someone how to
 * refactor dumb shitcode into something pleasant
 * to use and read.
 *
 * // TODO: REFACTOR THIS
 *
 * @package App\Notifications
 */
class SubscriptionPaymentsDue extends Notification
{
    use Queueable;

    /**
     * Notification contents
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $subscriptions;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Support\Collection $subscriptions
     */
    public function __construct(Collection $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return string
     */
    public function toTelegram()
    {
        return $this->prepareNotificationText($this->subscriptions);
    }

    /**
     * Prepare notification contents
     *
     * @param Collection $subscriptions
     *
     * @return string
     */
    protected function prepareNotificationText(Collection $subscriptions)
    {
        $text = 'Привет, @l\_fin. ' . PHP_EOL;

        if (now()->isMonday()) {
            $text .= PHP_EOL . 'Платежи на предстоящей неделе:';

            $subscriptions->filter(fn (Subscription $subscription) => $subscription->dueThisWeek())
                ->groupBy('service')
                ->each(function ($group, $service) use (&$text) {
                    $text .= PHP_EOL . $service . PHP_EOL;

                    $group->each(function (Subscription $subscription) use (&$text) {
                        $text .= $this->formatSubscription($subscription) . PHP_EOL;
                    });
                });
        }

        $subscriptions->filter(fn (Subscription $subscription) => $subscription->dueTomorrow())
            ->whenNotEmpty(function ($subscriptions) use (&$text) {
                $text .= PHP_EOL . 'Платежи на завтра:';

                return $subscriptions;
            })
            ->groupBy('service')
            ->each(function ($group, $service) use (&$text) {
                $text .= PHP_EOL . $service . PHP_EOL;

                $group->each(function (Subscription $subscription) use (&$text) {
                    $text .= $this->formatSubscription($subscription) . PHP_EOL;
                });
            });


        $subscriptions->filter(fn (Subscription $subscription) => $subscription->dueToday())
            ->whenNotEmpty(function ($subscriptions) use (&$text) {
                $text .= PHP_EOL . '*Платежи на СЕГОДНЯ:*';

                return $subscriptions;
            })
            ->groupBy('service')
            ->each(function ($group, $service) use (&$text) {
                $text .= PHP_EOL . $service . PHP_EOL;

                $group->each(function (Subscription $subscription) use (&$text) {
                    $text .= $this->formatSubscription($subscription) . PHP_EOL;
                });
            });

        return $text;
    }

    /**
     * Format subscription info
     *
     * @param Subscription $subscription
     *
     * @return string
     */
    protected function formatSubscription(Subscription $subscription)
    {
        return '- ' . $subscription->account . ' ' . $subscription->amount . ' ';
    }
}
