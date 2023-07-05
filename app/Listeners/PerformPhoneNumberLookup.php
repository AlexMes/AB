<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use App\Jobs\LookupPhoneNumber;

class PerformPhoneNumberLookup
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        try {
            LookupPhoneNumber::dispatchNow($event->lead);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
