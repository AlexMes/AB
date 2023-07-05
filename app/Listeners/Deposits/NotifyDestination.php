<?php

namespace App\Listeners\Deposits;

use App\Events\Deposits\Created;
use App\NotificationType;
use Illuminate\Support\Facades\Notification;

class NotifyDestination
{
    /**
     * Handle the event.
     *
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        $assignment = $event->deposit->getAssignment();
        if (
            optional(optional($assignment)->destination)->deposit_notification
            && optional($event->deposit->user)->hasTelegramNotification(NotificationType::DESTINATION_DEPOSIT)
            && !empty($event->deposit->user->telegram_id)
        ) {
            $recipients = [
                $event->deposit->user->telegram_id,
            ];

            Notification::send($recipients, new \App\Notifications\Deposit\Created($event->deposit));
        }
    }
}
