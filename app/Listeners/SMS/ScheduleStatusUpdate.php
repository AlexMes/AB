<?php

namespace App\Listeners\SMS;

use App\Events\Sms\SmsMessageCreated;

class ScheduleStatusUpdate
{

    /**
     * Handle the event.
     *
     * @param \App\Events\Sms\SmsMessageCreated $event
     *
     * @return void
     */
    public function handle(SmsMessageCreated $event)
    {
        $event->message->updateStatus(120);
    }
}
