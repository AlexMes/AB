<?php

namespace App\Listeners;

use App\Deposit;
use App\Events\LeadPulled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateDepositIfNotCreated implements ShouldQueue
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
        $assignment = $event->assignment;

        if ($this->leadMarkedAsConversion($assignment) && $this->leadDoesntHaveDepositYet($assignment)) {
            $this->createDepositFromLead($assignment);
        }
    }

    /**
     * Determines is assignment marked as deposit by manager
     *
     * @param \App\LeadOrderAssignment $assignment
     *
     * @return bool
     */
    protected function leadMarkedAsConversion(\App\LeadOrderAssignment $assignment)
    {
        return Str::contains($assignment->status, ['депозит','Депозит','Деп','деп']);
    }

    /**
     * Ensure that assignment is not converted yet
     *
     * @param \App\LeadOrderAssignment $assignment
     *
     * @return bool
     */
    protected function leadDoesntHaveDepositYet(\App\LeadOrderAssignment $assignment)
    {
        return $assignment->hasDeposit() === false;
    }

    /**
     * Create brand new deposit from the assignment
     *
     * @param \App\LeadOrderAssignment $assignment
     */
    protected function createDepositFromLead(\App\LeadOrderAssignment $assignment)
    {
        $deposit = Deposit::createFromAssignment($assignment);
        Log::channel('deposits')->debug(json_encode($deposit));
    }
}
