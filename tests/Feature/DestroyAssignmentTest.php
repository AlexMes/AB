<?php

namespace Tests\Feature;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DestroyAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuth()
    {
        Event::fake();

        $this->assertGuest();

        $assignment = factory(LeadOrderAssignment::class)->create();

        $this->deleteJson(route('api.assignments.destroy', ['assignment' => $assignment]))
            ->assertStatus(401);
    }

    /** @test */
    public function adminCanDeleteAssignment()
    {
        Event::fake();

        $this->signIn();

        $assignment = factory(LeadOrderAssignment::class)->create();

        $this->deleteJson(route('api.assignments.destroy', ['assignment' => $assignment]))
            ->assertStatus(204);

        $this->assertDatabaseCount('lead_order_assignments', 0);
    }

    /** @test */
    public function itCreatesUnassignedEvent()
    {
        Queue::fake();

        $this->signIn();

        $assignment = factory(LeadOrderAssignment::class)->create();

        $this->deleteJson(route('api.assignments.destroy', ['assignment' => $assignment]))
            ->assertStatus(204);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $assignment->lead_id,
            'eventable_type' => $assignment->lead->events()->getMorphClass(),
            'type'           => Lead::UNASSIGNED,
            'auth_id'        => $this->user->id,
            'auth_type'      => get_class($this->user),
        ]);
    }

    /** @test */
    public function itDecrementsReceived()
    {
        $this->signIn();

        $route      = factory(LeadOrderRoute::class)->state('incomplete')->create();
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);
        $this->assertEquals(1, $route->refresh()->leadsReceived);

        $this->deleteJson(route('api.assignments.destroy', ['assignment' => $assignment]))
            ->assertStatus(204);

        $route->refresh();
        $this->assertEquals(0, $route->refresh()->leadsReceived);
    }

    /** @test */
    public function itUpdatesResultLeadsCount()
    {
        $this->signIn();

        $route      = factory(LeadOrderRoute::class)->state('incomplete')->create();
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);
        $result     = Result::first();
        $this->assertEquals(1, $result->leads_count);

        $this->deleteJson(route('api.assignments.destroy', ['assignment' => $assignment]))
            ->assertStatus(204);

        $this->assertEquals(0, $result->refresh()->leads_count);
    }
}
