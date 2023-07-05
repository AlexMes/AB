<?php

namespace App\Listeners;

use App\Events\AssignmentSaved;
use App\LeadOrderAssignment;
use App\LeadResellBatch;
use App\ResellBatch;

class FinishResellBatch
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
        if (
            $event->assignment->hasDeliveryTry() && $event->assignment->batch !== null
            && $event->assignment->batch->status === ResellBatch::IN_PROCESS
        ) {
            $batchLeads = LeadResellBatch::query()
                ->whereBatchId($event->assignment->batch->id)
                ->get(['assignment_id']);
            if (
                LeadOrderAssignment::withDeliveryTry()
                    ->whereIn('id', $batchLeads->pluck('assignment_id')->filter())
                    ->count() === $batchLeads->count()
            ) {
                $event->assignment->batch->update(['status' => ResellBatch::FINISHED]);
            }
        }
    }
}
