<?php

namespace App\Listeners;

use App\Events\LeadDestinationUpdated;
use App\LeadDestination;
use App\LeadOrderAssignment;

class SetDestinationAssignmentStatus
{
    /**
     * Handle the event.
     *
     * @param LeadDestinationUpdated $event
     *
     * @return void
     */
    public function handle(LeadDestinationUpdated $event)
    {
        if ($event->destination->isDirty('status_map')) {
            foreach ($this->getChanges($event->destination) as $status) {
                LeadOrderAssignment::whereStatus($status['external'])
                    ->whereDestinationId($event->destination->id)
                    ->update(['status' => $status['internal']]);
            }
        }
    }

    /**
     * @param LeadDestination $destination
     *
     * @return array
     */
    protected function getChanges(LeadDestination $destination): array
    {
        $original = collect($destination->getOriginal('status_map'));
        $changed  = collect($destination->status_map);

        $actualChanged = [];

        foreach ($changed as $changedItem) {
            $originalItem = $original->where('external', $changedItem['external'])->first();
            if (!empty($changedItem['internal']) && (empty($originalItem) || empty($originalItem['internal']))) {
                $actualChanged[] = $changedItem;
            }
        }

        return $actualChanged;
    }
}
