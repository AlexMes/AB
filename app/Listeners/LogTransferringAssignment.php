<?php

namespace App\Listeners;

use App\Events\AssignmentTransferred;

class LogTransferringAssignment
{
    /**
     * Handle the event.
     *
     * @param AssignmentTransferred $event
     *
     * @return void
     */
    public function handle(AssignmentTransferred $event)
    {
        \Log::channel('transferred-assignments')->debug(
            "User #" . auth()->id() . " (" . (optional(auth()->user())->name ?? 'CLI') . ")" .
            " transferred assignment #{$event->assignment->id}" .
            " from manager #{$event->oldRoute->manager->id} ({$event->oldRoute->manager->name})" .
            " to #{$event->newRoute->manager->id} ({$event->newRoute->manager->name})"
        );
    }
}
