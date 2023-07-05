<?php

namespace Tests\Unit\Jobs;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class LeadAssignerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itAssignsLeadWithPassedRegisteredAt()
    {
        Event::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();
        /** @var Lead $lead */
        $lead         = factory(Lead::class)->create(['offer_id'  => $route->offer_id]);
        $registeredAt = now()->subDays(3);

        $this->mock(Pipeline::class, function (MockInterface $mock) use ($lead) {
            $mock->shouldReceive('send')->passthru();

            $mock->shouldReceive('through')->andReturnSelf();

            $mock->shouldReceive('then')->passthru();
        });

        LeadAssigner::dispatchNow($lead, null, null, $registeredAt);

        $this->assertDatabaseCount('lead_order_assignments', 1);
        $this->assertTrue($registeredAt->is(LeadOrderAssignment::first()->registered_at));
    }

    /** @test */
    public function itAssignsLeadToManagerOnlyOnce()
    {
        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();
        factory(LeadOrderRoute::class)->state('incomplete')->create([
            'offer_id'   => $route->offer_id,
            'manager_id' => $route->manager_id,
        ]);
        /** @var Lead $lead */
        $lead         = factory(Lead::class)->create(['offer_id'  => $route->offer_id]);
        $registeredAt = now()->subDays(3);

        $this->mock(Pipeline::class, function (MockInterface $mock) use ($lead) {
            $mock->shouldReceive('send')->passthru();

            $mock->shouldReceive('through')->andReturnSelf();

            $mock->shouldReceive('then')->passthru();
        });

        LeadAssigner::dispatchNow($lead, null, null, $registeredAt);
        LeadAssigner::dispatchNow($lead, null, null, $registeredAt);

        $this->assertDatabaseCount('lead_order_assignments', 1);
        $this->assertTrue($registeredAt->is(LeadOrderAssignment::first()->registered_at));
    }
}
