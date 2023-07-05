<?php

namespace Tests\Unit\Jobs\Leads;

use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\Jobs\Leads\SetBitrixStatus;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SetBitrixStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesDeposit()
    {
        Event::fake();
        Queue::fake();

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

        $mock = $this->mock(GetsInfoFromDestination::class);
        $mock->shouldReceive('getLead')->with($assignment)->andReturn([
            'STATUS_ID' => 'Депозит',
            'COMMENTS'  => 'yohoho',
        ]);

        SetBitrixStatus::dispatchNow($assignment, $mock);

        $this->assertTrue($assignment->hasDeposit());
        $this->assertEquals(150, $assignment->getDeposit()->sum);
        $this->assertEquals('yohoho', $assignment->comment);
    }

    /** @test */
    public function itDoesNotCreateDepositIfStatusIsNotAppropriate()
    {
        Event::fake();
        Queue::fake();

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

        $mock = $this->mock(GetsInfoFromDestination::class);
        $mock->shouldReceive('getLead')->with($assignment)->andReturn([
            'STATUS_ID' => 'Отказ',
            'COMMENTS'  => 'yohoho',
        ]);

        SetBitrixStatus::dispatchNow($assignment, $mock);

        $this->assertFalse($assignment->hasDeposit());
        $this->assertEquals('yohoho', $assignment->comment);
    }
}
