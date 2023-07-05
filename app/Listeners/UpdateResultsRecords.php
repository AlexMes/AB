<?php

namespace App\Listeners;

use App\Events\LeadAssigned;
use App\Result;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateResultsRecords implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param \App\Events\LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        $result = Result::whereDate('date', $event->assignment->created_at)
            ->where('offer_id', $event->assignment->route->offer_id)
            ->where('office_id', $event->assignment->route->order->office_id)
            ->first();

        if ($result === null) {
            $result  = Result::create([
                'date'      => $event->assignment->created_at->toDateString(),
                'offer_id'  => $event->assignment->route->offer_id,
                'office_id' => $event->assignment->route->order->office_id
            ]);
        }

        return $result->updateLeadsCount();
    }
}
