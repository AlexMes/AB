<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class UpdateReceivalTimestamp
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
        $event->assignment->route()->update([
            'last_received_at' => now()
        ]);
    }
}
