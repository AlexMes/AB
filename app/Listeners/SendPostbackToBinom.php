<?php

namespace App\Listeners;

use App\Events\Deposits\Created;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPostbackToBinom implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Deposits\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if ($event->deposit->lead_id === null) {
            return;
        }

        if ($event->deposit->lead->click()->doesntExist()) {
            return;
        }

        $event->deposit->lead->click->sendPayout($event->deposit->lead->deposits()->sum('benefit'));
    }
}
