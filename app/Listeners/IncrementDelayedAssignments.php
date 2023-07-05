<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class IncrementDelayedAssignments
{
    /**
     * Handle the event.
     *
     * @param LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        if ($event->assignment->deliver_at !== null) {
            $event->assignment->getLeadsOrder()->increment('delayed_assignments');
        }
    }
}
