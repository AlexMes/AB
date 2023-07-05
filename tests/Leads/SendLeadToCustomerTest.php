<?php

namespace Leads;

use App\Lead;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\Leads\SendLeadToCustomer;
use App\Offer;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use LeadDestinationSeeder;
use PresetLeadAssignments;
use Tests\TestCase;

class SendLeadToCustomerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function itDoesNotAssignLeadWhenItNotDelivered()
    {
        $this->seed([PresetLeadAssignments::class, LeadDestinationSeeder::class]);
        Event::fake();

        $destination = LeadDestination::whereDriver(LeadDestinationDriver::DEFAULT)->first();

        $lead  = factory(Lead::class)->state('valid')->create(['offer_id' => Offer::first()->id]);
        Office::query()->update(['destination_id' => $destination->id]);

        $this->app->bind($destination->service->implementation, function ($app) use ($destination) {
            $mock = \Mockery::mock($destination->service->implementation);
            $mock->shouldReceive('send')->once();
            $mock->shouldReceive('isDelivered')->andReturnFalse();
            $mock->shouldReceive('getRedirectUrl')->andReturnNull();
            $mock->shouldReceive('getError')->andReturnNull();
            $mock->shouldReceive('getExternalId')->andReturnNull();

            return $mock;
        });

        $assignment = SendLeadToCustomer::dispatchNow($lead);
        $this->assertNull($assignment);
        $this->assertDatabaseCount('lead_order_assignments', 0);
    }

    /** @test */
    public function itAssignsLeadWhenDriverIsGoogle()
    {
        $this->seed([PresetLeadAssignments::class, LeadDestinationSeeder::class]);
        Event::fake();

        $destination = LeadDestination::whereDriver(LeadDestinationDriver::GSHEETS)->first();

        $lead  = factory(Lead::class)->state('valid')->create(['offer_id' => Offer::first()->id]);
        Office::query()->update(['destination_id' => $destination->id]);

        $this->app->bind($destination->service->implementation, function ($app) use ($destination) {
            $mock = \Mockery::mock($destination->service->implementation);
            $mock->shouldReceive('send')->once();
            $mock->shouldReceive('isDelivered')->andReturnFalse();
            $mock->shouldReceive('getRedirectUrl')->andReturnNull();
            $mock->shouldReceive('getError')->andReturnNull();
            $mock->shouldReceive('getExternalId')->andReturnNull();

            return $mock;
        });

        $assignment = SendLeadToCustomer::dispatchNow($lead);
        $this->assertNotNull($assignment);
        $this->assertDatabaseCount('lead_order_assignments', 1);
    }

    /** @test */
    public function itAssignsLeadWhenItDelivered()
    {
        $this->seed([PresetLeadAssignments::class, LeadDestinationSeeder::class]);
        Event::fake();

        $destination = LeadDestination::whereDriver(LeadDestinationDriver::DEFAULT)->first();

        $lead  = factory(Lead::class)->state('valid')->create(['offer_id' => Offer::first()->id]);
        Office::query()->update(['destination_id' => $destination->id]);

        $this->app->bind($destination->service->implementation, function ($app) use ($destination) {
            $mock = \Mockery::mock($destination->service->implementation);
            $mock->shouldReceive('send')->once();
            $mock->shouldReceive('isDelivered')->andReturnTrue();
            $mock->shouldReceive('getRedirectUrl')->andReturnNull();
            $mock->shouldReceive('getError')->andReturnNull();
            $mock->shouldReceive('getExternalId')->andReturnNull();

            return $mock;
        });

        $assignment = SendLeadToCustomer::dispatchNow($lead);
        $this->assertNotNull($assignment);
        $this->assertDatabaseCount('lead_order_assignments', 1);
    }
}
