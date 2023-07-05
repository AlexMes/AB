<?php

namespace App\Listeners;

use App\Events\LeadAssigned;
use Illuminate\Support\Facades\DB;

class MarkAssignmentOnResellPivot
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        $resold = DB::table('lead_resell_batch')
            ->whereNull('assignment_id')
            ->whereLeadId($event->assignment->lead_id)
            ->first();

        if ($resold) {
            DB::table('lead_resell_batch')
                ->where('id', $resold->id)
                ->update(['assigned_at' => $event->assignment->created_at, 'assignment_id' => $event->assignment->id]);
        }
    }
}
