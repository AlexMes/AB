<?php

namespace App\Listeners;

use App\Events\AssignmentUpdating;

class UnsetConfirmedOnAssignment
{
    /**
     * Handle the event.
     *
     * @param AssignmentUpdating $event
     *
     * @return void
     */
    public function handle(AssignmentUpdating $event)
    {
        if (
            $event->assignment->confirmed_at !== null && in_array($event->assignment->status, ['Дубль'])
            && $event->assignment->getLeadsOrder()->branch_id === 16
            && $event->assignment->created_at->floorDay()->greaterThanOrEqualTo(now()->startOfMonth())
        ) {
            $event->assignment->confirmed_at = null;
        }
    }
}
