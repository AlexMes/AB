<?php

namespace Tests\Feature;

use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function orderMustHaveOffice()
    {
        $order = factory(LeadsOrder::class)->create();

        $this->assertNotNull($order->office);
    }

    /** @test */
    public function itHasRoutes()
    {
        $order = factory(LeadsOrder::class)->create();

        factory(LeadOrderRoute::class)->create([
            'order_id' => $order->id,
        ]);

        $this->assertCount(1, $order->routes);
    }
}
