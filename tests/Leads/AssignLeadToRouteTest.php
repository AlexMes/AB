<?php

namespace Leads;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Leads\AssignLeadToRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AssignLeadToRouteTest extends TestCase
{
    use RefreshDatabase;

    protected $lead;
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->lead  = factory(Lead::class)->create();
        $this->route = factory(LeadOrderRoute::class)->create();
    }

    /** @test */
    public function itReturnsAssignment()
    {
        $assignment = AssignLeadToRoute::dispatchNow($this->lead, $this->route);
        $this->assertInstanceOf(LeadOrderAssignment::class, $assignment, 'Wrong object returned');
    }

    /** @test */
    public function assignmentHasCorrectBindings()
    {
        $assignment = AssignLeadToRoute::dispatchNow($this->lead, $this->route);

        $this->assertEquals($this->lead->id, $assignment->lead_id, 'Wrong lead');
        $this->assertEquals($this->route->id, $assignment->route_id, 'Wrong route');
    }

    /** @test */
    public function itDecrementsLeadsReceivedOnRoute()
    {
        $before = $this->route->leadsReceived;

        AssignLeadToRoute::dispatchNow($this->lead, $this->route);

        $this->assertGreaterThan($before, $this->route->leadsReceived);
    }

    /** @test */
    public function itUpdatesRouteMarksWithCurrentTimestamp()
    {
        $this->assertNull($this->route->last_received_at);

        AssignLeadToRoute::dispatchNow($this->lead, $this->route);

        $this->assertNotNull($this->route->last_received_at, 'Last received at timestamp is not updated');
        $this->assertTrue(Carbon::parse($this->route->last_received_at)->isCurrentSecond());
    }

    /** @test */
    public function itCreatesAssignmentLogEntryOnLead()
    {
        $this->assertNull($this->lead->events()->where('type', Lead::ASSIGNED)->first());

        AssignLeadToRoute::dispatchNow($this->lead, $this->route);

        /** @var \App\Event $entry */
        $entry = $this->lead->events()->where('type', Lead::ASSIGNED)->first();

        $this->assertNotNull($entry, 'Assignment is not logged.');
        $this->assertEquals($this->route->manager_id, $entry->custom_data['manager_id'], 'Wrong manager logged');
        $this->assertEquals($this->route->order_id, $entry->custom_data['order_id'], 'Wrong order logged');
        $this->assertEquals($this->route->offer_id, $entry->custom_data['offer_id'], 'Wrong offer logged');
    }
}
