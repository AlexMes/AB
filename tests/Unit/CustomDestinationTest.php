<?php

namespace Tests\Unit;

use App\DestinationDrivers\Crm;
use App\LeadDestination;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Tests\TestCase;

class CustomDestinationTest extends TestCase
{
    /** @test */
    public function orderRouteAwareAboutCustomDestinations()
    {
        $route = new LeadOrderRoute([
            'order_id'       => 1,
            'manager_id'     => 2,
            'destination_id' => null
        ]);

        $this->assertFalse($route->hasCustomDestination());
    }

    /** @test */
    public function leadsOrderAwareAboutCustomDestinations()
    {
        $order = new LeadsOrder([
            'office_id'       => 1,
            'date'            => now(),
            'destination_id'  => null
        ]);

        $this->assertFalse($order->hasCustomDestination());
    }

    /** @test */
    public function defaultDestinationExists()
    {
        $order      = new LeadsOrder();
        $route      = new LeadOrderRoute();
        $assignment = new LeadOrderAssignment([
            'lead_id'  => 1234,
            'route_id' => 1234,
        ]);

        $route->setRelation('order', $order);
        $assignment->setRelation('route', $route);

        $this->assertFalse($route->hasCustomDestination());
        $this->assertFalse($order->hasCustomDestination());

        $this->assertInstanceOf(LeadDestination::class, $assignment->getTargetDestination());
    }

    /** @test */
    public function defaultDestinationIsCrm()
    {
        $order      = new LeadsOrder();
        $route      = new LeadOrderRoute();
        $assignment = new LeadOrderAssignment([
            'lead_id'  => 1234,
            'route_id' => 1234,
        ]);

        $route->setRelation('order', $order);
        $assignment->setRelation('route', $route);

        $this->assertFalse($route->hasCustomDestination());
        $this->assertFalse($order->hasCustomDestination());

        $this->assertInstanceOf(Crm::class, $assignment->getTargetDestination()->initialize());
    }
}
