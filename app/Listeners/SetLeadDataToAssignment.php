<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class SetLeadDataToAssignment
{
    /**
     * Handle the event.
     *
     * @param LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        $event->assignment->update([
            'gender_id' => $event->assignment->lead->gender_id,
        ]);
    }
}
