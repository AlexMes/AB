<?php

namespace App\Listeners;

use App\Events\LeadPulled;

class SetLeadBenefit
{
    /**
     * Handle the event.
     *
     * @param \App\Events\LeadPulled $event
     *
     * @return void
     */
    public function handle(LeadPulled $event)
    {
        if (in_array($event->assignment->status, ['Дубль', 'Неверный номер'])) {
            return;
        }

        $event->assignment->updateBenefit();
    }
}
