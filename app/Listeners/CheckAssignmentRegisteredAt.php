<?php

namespace App\Listeners;

use App\Events\LeadAssigning;

class CheckAssignmentRegisteredAt
{
    /**
     * Handle the event.
     *
     * @param LeadAssigning $event
     *
     * @return void
     */
    public function handle(LeadAssigning $event)
    {
        if ($event->assignment->route->order->office->is_cp) {
            $event->assignment->registered_at = now()->subSeconds(rand(2, 5));
        }
    }
}
