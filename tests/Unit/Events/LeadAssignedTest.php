<?php

namespace Tests\Unit\Events;

use App\Events\Lead\Created as LeadCreated;
use App\Events\LeadAssigned;
use App\Jobs\Leads\DetectGender;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\OfficePayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LeadAssignedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDispatches()
    {
        Event::fake();

        factory(LeadOrderAssignment::class)->create();

        Event::assertDispatched(LeadAssigned::class);
    }

    /** @test */
    public function itCopiesGenderIdFromLead()
    {
        Event::fake([LeadCreated::class]);
        Bus::fake();

        $lead = factory(Lead::class)->create(['gender_id' => 2]);
        Bus::assertNotDispatched(DetectGender::class);

        $assignment = factory(LeadOrderAssignment::class)->create(['lead_id' => $lead->id]);

        $this->assertEquals($lead->gender_id, $assignment->fresh()->gender_id);
    }

    /** @test */
    public function itIncrementsAssignedOnOfficePayment()
    {
        Event::fake([LeadCreated::class]);
        Bus::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create();
        factory(OfficePayment::class)->state('incomplete')->create(['office_id' => $route->order->office_id]);

        /** @var OfficePayment $payment */
        $payment = $route->order->office->payments->first();
        factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertEquals($payment->assigned + 1, $route->fresh()->order->office->payments->first()->assigned);
    }
}
