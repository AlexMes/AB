<?php

namespace App\Listeners\Leads;

use App\Events\Lead\Created;
use App\Lead;
use App\Rules\ObsceneCensorRus;
use Illuminate\Contracts\Queue\ShouldQueue;

class ValidateNames implements ShouldQueue
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
        $event->lead->update([
            'valid' => $this->withoutAbuse($event->lead)
        ]);
    }

    /**
     * Determine is lead name contains abusive words
     *
     * @param \App\Lead $lead
     *
     * @return mixed
     */
    protected function withoutAbuse(Lead $lead)
    {
        return ObsceneCensorRus::isAllowed($lead->fullname);
    }
}
