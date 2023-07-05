<?php

namespace App\Events;

use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentTransferred
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var LeadOrderAssignment
     */
    public LeadOrderAssignment $assignment;

    /**
     * @var LeadOrderRoute
     */
    public LeadOrderRoute $oldRoute;

    /**
     * @var LeadOrderRoute
     */
    public LeadOrderRoute $newRoute;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LeadOrderAssignment $assignment, LeadOrderRoute $old, LeadOrderRoute $new)
    {
        $this->assignment   = $assignment;
        $this->oldRoute     = $old;
        $this->newRoute     = $new;
    }
}
