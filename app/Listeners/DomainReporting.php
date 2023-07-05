<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use App\Jobs\Leads\VerifyOfferForReport;

class DomainReporting
{
    /**
     * Handle the event.
     *
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        VerifyOfferForReport::dispatch($event->lead);
    }
}
