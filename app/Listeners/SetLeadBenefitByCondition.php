<?php

namespace App\Listeners;

use App\Events\AssignmentSaved;

class SetLeadBenefitByCondition
{
    /**
     * Handle the event.
     *
     * @param AssignmentSaved $event
     *
     * @return void
     */
    public function handle(AssignmentSaved $event)
    {
        if ($event->assignment->isConfirmed() && $event->assignment->getOriginal('confirmed_at') === null) {
            $event->assignment->updateBenefit();
        }
    }
}
