<?php

namespace App\Events;

use App\LeadOrderAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentUpdating
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var \App\LeadOrderAssignment
     */
    public LeadOrderAssignment $assignment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }
}
