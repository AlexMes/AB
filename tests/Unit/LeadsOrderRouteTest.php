<?php

namespace Tests\Unit;

use App\LeadOrderRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadsOrderRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function scopeIncompleteOk()
    {
        factory(LeadOrderRoute::class, 3)->state('incomplete')->create();

        $this->assertCount(3, LeadOrderRoute::incomplete()->get());
    }

    /** @test */
    public function scopeCurrentOk()
    {
        factory(LeadOrderRoute::class, 3)->create();

        $this->assertCount(3, LeadOrderRoute::current()->get());
    }

    /** @test */
    public function scopeCompleteOk()
    {
        factory(LeadOrderRoute::class, 3)->state('completed')->create();

        $this->assertCount(3, LeadOrderRoute::completed()->get());
    }

    /** @test */
    public function twoScopesTogetherOk()
    {
        factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertCount(1, LeadOrderRoute::current()->incomplete()->get());
    }

    /** @test */
    public function offerFilterOk()
    {
        factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertCount(1, LeadOrderRoute::current()->incomplete()->where('offer_id', 1)->get());
    }

    /** @test */
    public function isCompleteOk()
    {
        $route = factory(LeadOrderRoute::class)->state('completed')->create();

        $this->assertTrue($route->isCompleted());
    }

    /** @test */
    public function isIncompleteOk()
    {
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertTrue($route->isIncomplete());
    }
}
