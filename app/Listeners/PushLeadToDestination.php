<?php

namespace App\Listeners;

use App\Events\LeadAssigned;
use App\Jobs\DeliverAssignment;
use Cache;

class PushLeadToDestination
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
        if (Cache::lock('los-'. $event->assignment->id, 3)->get()) {
            DeliverAssignment::dispatch($event->assignment);
        }
    }
}
