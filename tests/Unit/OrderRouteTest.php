<?php

namespace Tests\Unit;

use App\LeadOrderRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function incompleteScopeOk()
    {
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertTrue($route->is(LeadOrderRoute::incomplete()->first()));
    }
}
