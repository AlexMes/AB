<?php

namespace App\Leads;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignLeadToRoute
{
    use Dispatchable;

    /**
     * Lead to assign
     *
     * @var \App\Lead
     */
    protected Lead $lead;

    /**
     * Route to assign lead to
     *
     * @var \App\LeadOrderRoute
     */
    protected LeadOrderRoute $route;

    /**
     * AssignLeadToRoute constructor.
     *
     * @param \App\Lead           $lead
     * @param \App\LeadOrderRoute $route
     *
     * @return void
     */
    public function __construct(Lead $lead, LeadOrderRoute $route)
    {
        $this->lead  = $lead;
        $this->route = $route;
    }

    /**
     * Create an assignment
     *
     * @throws \Throwable
     *
     * @return \App\LeadOrderAssignment
     *
     *
     */
    public function handle(): LeadOrderAssignment
    {
        /** @var \App\LeadOrderAssignment $assignment */
        $assignment = $this->route
            ->assignments()
            ->make(['lead_id' => $this->lead->id, 'registered_at' => $this->lead->created_at, 'is_live' => true]);

        $this->route->update([
            'leadsReceived'    => $this->route->leadsReceived + 1,
            'last_received_at' => now(),
        ]);

        $this->lead->addEvent(Lead::ASSIGNED, [
            'manager_id'     => $this->route->manager_id,
            'office_id'      => $this->route->order->office_id,
            'order_id'       => $this->route->order_id,
            'offer_id'       => $this->route->offer_id,
            'destination_id' => $assignment->destination_id
        ]);

        $assignment->save();

        return $assignment;
    }
}
