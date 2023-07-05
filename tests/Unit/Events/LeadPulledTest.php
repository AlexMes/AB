<?php

namespace Tests\Unit\Events;

use App\Deposit;
use App\Events\LeadPulled;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPulledTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesDeposit()
    {
        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'route_id'    => $route->id,
            'deposit_sum' => 150,
            'status'      => 'Депозит',
        ]);

        LeadPulled::dispatch($assignment);

        $this->assertTrue($assignment->fresh()->hasDeposit());
    }

    /** @test */
    public function itDoesNotCreateDepositIfItExists()
    {
        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'route_id'    => $route->id,
            'deposit_sum' => 150,
            'status'      => 'Депозит',
        ]);
        Deposit::createFromAssignment($assignment);

        LeadPulled::dispatch($assignment);

        $this->assertDatabaseCount('deposits', 1);
    }

    /** @test */
    public function itMarksAssignmentAsConfirmed()
    {
        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);
        $assignment->update(['confirmed_at' => null]);

        $this->assertNull($assignment->refresh()->confirmed_at);
        LeadPulled::dispatch($assignment);
        $this->assertNotNull($assignment->refresh()->confirmed_at);
    }

    /** @test */
    public function itUpdateAssignmentBenefit()
    {
        $office  = factory(Office::class)->create(['cpl' => 133]);
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);
        $assignment->update(['confirmed_at' => null]);

        $this->assertNull($assignment->refresh()->benefit);
        LeadPulled::dispatch($assignment);
        $this->assertEquals($office->cpl, $assignment->refresh()->benefit);
    }
}
