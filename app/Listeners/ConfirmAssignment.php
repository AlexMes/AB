<?php

namespace App\Listeners;

use App\Events\LeadPulled;

class ConfirmAssignment
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(LeadPulled $event)
    {
        if (!$event->assignment->isConfirmed()) {
            $event->assignment->markAsConfirmed();
        }
    }
}
