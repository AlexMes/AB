<?php

namespace Tests\Feature;

use App\Events\LeadAssigned;
use App\Jobs\DeliverAssignment;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Listeners\AssignLeadToManager;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LeadAssignmentPipelineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDispatchesAssignment()
    {
        Queue::fake();
        factory(Lead::class)->create();

        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class === AssignLeadToManager::class;
        });
    }

    /** @test */
    public function itDispatchesAssignmentEvent()
    {
        \Event::fake();
        $assignment = factory(LeadOrderAssignment::class)->create();

        Event::assertDispatched(LeadAssigned::class);
    }

    /** @test */
    public function itIncrementsReceivedLeadsAmount()
    {
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertEquals(0, $route->leadsReceived, 'Non 0 route');

        factory(LeadOrderAssignment::class)->create([
            'route_id' => $route->id,
        ]);

        $this->assertEquals(1, $route->fresh()->leadsReceived, 'Amount are not incremented');
    }

    /** @test */
    public function itUpdatedLastReceivedAtTimestamp()
    {
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        $this->assertNull($route->last_received_at, 'Non 0 route');

        factory(LeadOrderAssignment::class)->create([
            'route_id' => $route->id,
        ]);

        $this->assertNotNull($route->fresh()->last_received_at, 'Timestamp are not updated');
    }

    /** @test */
    public function itPushesLeadToDestination()
    {
        Queue::fake();
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        $assignment = factory(LeadOrderAssignment::class)->create([
            'route_id' => $route->id,
        ]);

        Queue::assertPushed(DeliverAssignment::class, fn ($job) => $job->assignment->is($assignment));
    }

    /** @test */
    public function itUpdatesOfficeResults()
    {
        $route  = factory(LeadOrderRoute::class)->state('incomplete')->create();

        factory(LeadOrderAssignment::class)->create([
            'route_id' => $route->id,
        ]);

        $office = $route->order->office;
        $this->assertNotEmpty($office->results, 'Result was not created');
        $this->assertEquals(1, $office->results->first()->leads_count);
    }
}
