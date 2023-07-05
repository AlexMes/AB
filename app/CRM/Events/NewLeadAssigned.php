<?php

namespace App\CRM\Events;

use App\LeadOrderAssignment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLeadAssigned implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh assignment
     *
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
        $this->assignment = $assignment->load('lead', 'route');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(sprintf(
            'CRM.Manager.%d',
            $this->assignment->route->manager_id
        ));
    }

    /**
     * Define data that will be broadcasted
     *
     * @return array[]
     */
    public function broadcastWith()
    {
        return [
            'fullname' => $this->assignment->lead->fullname,
            'url'      => route('crm.assignments.show', $this->assignment),
        ];
    }
}
