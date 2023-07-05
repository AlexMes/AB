<?php

namespace App\Listeners;

use App\CRM\Events\NewLeadAssigned;
use App\Events\LeadAssigned;

class BroadcastAssignmentToManager
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
        NewLeadAssigned::dispatch($event->assignment);
    }
}
