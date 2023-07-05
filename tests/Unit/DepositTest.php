<?php

namespace Tests\Unit;

use App\Deposit;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesDepositFromAssignment()
    {
        Event::fake();
        Queue::fake();

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create();

        Deposit::createFromAssignment($assignment);

        $this->assertCount(1, Deposit::all());
    }

    /** @test */
    public function itDoesNotCreateDepositFromAssignmentIfItAlreadyHasOne()
    {
        Event::fake();
        Queue::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        Deposit::createFromAssignment($assignment);
        Deposit::createFromAssignment($assignment);

        $this->assertCount(1, Deposit::all());
    }

    /** @test */
    public function itUpdatesDepositFromAssignmentIfItExists()
    {
        Event::fake();
        Queue::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'deposit_sum' => 100]);

        Deposit::createFromAssignment($assignment);
        $this->assertEquals(100, $assignment->getDeposit()->sum);

        $assignment->deposit_sum = 300;
        Deposit::createFromAssignment($assignment);
        $this->assertEquals(300, $assignment->getDeposit()->sum);
    }
}
