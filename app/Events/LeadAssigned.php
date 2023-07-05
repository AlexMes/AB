<?php

namespace App\Events;

use App\LeadOrderAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadAssigned
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
     * @param \App\LeadOrderAssignment $assignment
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }
}
