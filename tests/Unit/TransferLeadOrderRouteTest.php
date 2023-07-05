<?php

namespace Tests\Unit;

use App\Jobs\TransferLeadOrderRoute;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Manager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransferLeadOrderRouteTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function itDispatchesTransferJob()
    {
        Queue::fake();

        $route   = factory(LeadOrderRoute::class)->create();
        $manager = factory(Manager::class)->create();

        $route->transfer($manager);

        Queue::assertPushed(
            TransferLeadOrderRoute::class,
            fn ($job) => $job->route->is($route) && $job->manager->is($manager),
            'Transfer job was not dispatched to queue'
        );
    }

    /** @test */
    public function itCreatesNewRouteForTargetManagerWhenRequired()
    {
        // Disable events, to speed up the test
        \Event::fake();

        /** @var LeadOrderRoute $route */
        $route       = factory(LeadOrderRoute::class)->create();

        /** @var Manager $manager */
        $manager = factory(Manager::class)->create();

        $route->transfer($manager);

        // Make sure, that new manager got his route
        $this->assertTrue($manager->routes()->where('offer_id', $route->offer_id)->exists());
    }

    /** @test */
    public function itReuseExistingRouteForTargetManagerWhenRequired()
    {
        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create();

        /** @var Manager $manager */
        $manager = factory(Manager::class)->create();

        $manager->routes()->create([
            'offer_id' => $route->offer_id,
            'order_id' => $route->order_id,
        ]);

        $route->transfer($manager);

        // Make sure, that we doesnt create another route
        $this->assertEquals(1, $manager->routes()->where('offer_id', $route->offer_id)->count());
    }

    /** @test */
    public function itTransfersOrderedAmountToTheTargetRoute()
    {
        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create([
            'leadsOrdered' => 5
        ]);

        /** @var LeadOrderRoute $targetRoute */
        $targetRoute = factory(LeadOrderRoute::class)->create([
            'order_id'     => $route->order_id,
            'offer_id'     => $route->offer_id,
            'leadsOrdered' => 10
        ]);

        $this->assertFalse($route->manager->is($targetRoute->manager), 'WTF, same manager, abort');

        $route->transfer($targetRoute->manager);

        $this->assertEquals(0, $route->refresh()->leadsOrdered, 'Old route order not cleared');
        $this->assertEquals(15, $targetRoute->refresh()->leadsOrdered, 'New route order not updated');
    }

    /** @test */
    public function itTransfersReceivedAmountToTheTargetRoute()
    {
        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create([
            'leadsReceived' => 5
        ]);

        /** @var LeadOrderRoute $targetRoute */
        $targetRoute = factory(LeadOrderRoute::class)->create([
            'order_id'             => $route->order_id,
            'offer_id'             => $route->offer_id,
            'leadsReceived'        => 10
        ]);

        $route->transfer($targetRoute->manager);

        $this->assertEquals(0, $route->fresh()->leadsReceived);
        $this->assertEquals(15, $targetRoute->fresh()->leadsReceived);
    }

    /** @test */
    public function itTransfersAssignmentsToTheTargetRoute()
    {
        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create([
            'leadsOrdered' => 5
        ]);
        \Event::fake();
        $assignments = factory(LeadOrderAssignment::class, 3)->create([
            'route_id' => $route->id,
        ]);

        /** @var Manager $manager */
        $manager = factory(Manager::class)->create();

        $route->transfer($manager);

        $targetRoute = $manager->routes()->first();

        foreach ($assignments as $assignment) {
            $this->assertTrue($assignment->fresh()->route->is($targetRoute), 'Assignment was not transferred.');
        }
    }
}
