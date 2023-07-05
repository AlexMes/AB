<?php

namespace App\Listeners;

use App\Events\LeadAssigned;
use App\Jobs\CheckLeadOnFrx;

class CheckForDuplicationOnFrx
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
        if ($event->assignment->destination_id === null) {
            CheckLeadOnFrx::dispatch($event->assignment);
        }
    }
}
