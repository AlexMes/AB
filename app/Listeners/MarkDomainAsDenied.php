<?php

namespace App\Listeners;

use App\Domain;
use App\Events\AdDisapproved;

class MarkDomainAsDenied
{
    /**
     * Handle the event.
     *
     * @param \App\Events\AdDisapproved $event
     *
     * @return void
     */
    public function handle(AdDisapproved $event)
    {
        if ($event->ad->hasDomain()) {
            $event->ad->domain->update(['reach_status' => Domain::MISSED]);
        }
    }
}
