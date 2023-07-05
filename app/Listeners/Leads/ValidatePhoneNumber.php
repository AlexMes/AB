<?php

namespace App\Listeners\Leads;

use App\Events\Lead\Created;
use App\Lead;
use App\PhoneLookup;
use Illuminate\Contracts\Queue\ShouldQueue;

class ValidatePhoneNumber implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Lead\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if ($event->lead->phone === null && $event->lead->domain === 'webinariys.com') {
            // do nothing
            return;
        }

        $event->lead->recordAs(Lead::PHONE_VALIDATED)->update([
            'phone_valid' => PhoneLookup::wherePhone($event->lead->phone)->exists(),
        ]);
    }
}
