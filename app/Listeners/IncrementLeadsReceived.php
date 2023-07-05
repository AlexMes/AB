<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class IncrementLeadsReceived
{
    /**
     * Handle the event.
     *
     * @param \App\Events\LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        // $event->assignment->route()->increment('leadsReceived');
    }
}
