<?php

namespace Tests\Unit\Jobs\Leads;

use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\Jobs\Leads\SetBitrixComments;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SetBitrixCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesLeadComments()
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
        $mock->shouldReceive('getLeadComments')->with($assignment)->andReturn([
            ['CREATED' => now()->subDays(1), 'COMMENT' => 'text1'],
        ]);

        SetBitrixComments::dispatchNow($assignment, $mock);

        $this->assertEquals(1, $assignment->lead->comments()->count());
        $this->assertDatabaseHas('comments', [
            'commentable_type' => $assignment->lead->getMorphClass(),
            'commentable_id'   => $assignment->lead->id,
            'user_id'          => null,
            'text'             => 'text1',
            'created_at'       => now()->subDays(1),
        ]);
    }
}
