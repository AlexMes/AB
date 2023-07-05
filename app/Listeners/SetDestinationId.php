<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class SetDestinationId
{
    /**
     * Handle the event
     *
     * @param \App\Events\LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        $event->assignment->recordDestinationId();
    }
}
