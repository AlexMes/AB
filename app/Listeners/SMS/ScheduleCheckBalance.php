<?php

namespace App\Listeners\SMS;

use App\AdsBoard;
use App\Events\Sms\SmsMessageCreated;
use App\Jobs\SMS\CheckBalance;
use Illuminate\Support\Facades\Cache;

class ScheduleCheckBalance
{
    /**
     * Handle the event.
     *
     * @param SmsMessageCreated $event
     *
     * @return void
     */
    public function handle(SmsMessageCreated $event)
    {
        if (
            $event->message->campaign->branch !== null
            && Cache::lock('sms-check-balance-' . $event->message->campaign->branch->id, 60 * 60)->get()
        ) {
            CheckBalance::dispatch($event->message->campaign->branch)->onQueue(AdsBoard::QUEUE_NOTIFICATIONS);
        }
    }
}
