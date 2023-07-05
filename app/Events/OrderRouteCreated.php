<?php

namespace App\Events;

use App\LeadOrderRoute;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRouteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Order route
     *
     * @var LeadOrderRoute
     */
    public $route;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LeadOrderRoute $route)
    {
        $this->route = $route->loadMissing('offer', 'manager', 'destination');
    }
}
