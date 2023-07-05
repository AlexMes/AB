<?php

namespace App\Listeners;

use App\Events\ClickCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPostbackLeadToBinom implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param ClickCreated $event
     *
     * @return void
     */
    public function handle(ClickCreated $event)
    {
        if ($event->click->lead->hasAffiliate()) {
            return;
        }

        $event->click->sendLeadReceived();
    }
}
