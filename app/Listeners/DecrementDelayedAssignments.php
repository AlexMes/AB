<?php

namespace App\Listeners;

use App\Events\AssignmentDeleted;

class DecrementDelayedAssignments
{
    /**
     * Handle the event.
     *
     * @param AssignmentDeleted $event
     *
     * @return void
     */
    public function handle(AssignmentDeleted $event)
    {
        if ($event->assignment->deliver_at !== null) {
            $event->assignment->getLeadsOrder()->decrement('delayed_assignments');
        }
    }
}
