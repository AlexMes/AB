<?php

namespace App\Listeners;

use App\Domain;

class MarkDomainAsPassed
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        if ($event->ad->hasDomain()) {
            $event->ad->domain->update(['reach_status' => Domain::PASSED]);
        }
    }
}
