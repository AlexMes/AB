<?php

namespace App\Events;

use App\LeadDestination;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadDestinationUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var LeadDestination
     */
    public LeadDestination $destination;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LeadDestination $destination)
    {
        $this->destination = $destination;
    }
}
