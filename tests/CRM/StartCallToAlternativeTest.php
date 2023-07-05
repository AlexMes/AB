<?php

namespace Tests\CRM;

use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class StartCallToAlternativeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesCallbackOnSuccess()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);

        $this->setGuard('crm')->signIn($manager);

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'alt_phone' => '532534543']);

        $callId = Str::random();
        Http::fake([
            '*' => Http::response([
                'call_id' => $callId,
                'message' => 'ok',
            ]),
        ]);

        $this->assertDatabaseCount('callbacks', 0);

        $this->get(route('crm.assignment.call-alt', $assignment));

        $this->assertCount(1, $assignment->callbacks);
        $this->assertEquals($assignment->formatted_alt_phone, $assignment->callbacks->first()->phone);
        $this->assertNull($assignment->callbacks->first()->call_at);
        $this->assertNotNull($assignment->callbacks->first()->called_at);
        $this->assertEquals($callId, $assignment->callbacks->first()->frx_call_id);
    }

    /** @test */
    public function itUpdatesIncompleteCallbackOnSuccess()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);

        $this->setGuard('crm')->signIn($manager);

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'alt_phone' => '532534543']);

        $callId = Str::random();
        Http::fake([
            '*' => Http::response([
                'call_id' => $callId,
                'message' => 'ok',
            ]),
        ]);

        $callback = tap($assignment->actualCallback()->fill(['call_at' => now()]))->save();
        $this->assertNull($callback->called_at);

        $this->get(route('crm.assignment.call-alt', $assignment));

        $this->assertNotNull($callback->refresh()->called_at);
        $this->assertDatabaseCount('callbacks', 1);
    }

    /** @test */
    public function itCreatesNewCallbackIfOldOneIsCompletedOnSuccess()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);

        $this->setGuard('crm')->signIn($manager);

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'alt_phone' => '532534543']);

        $callId = Str::random();
        Http::fake([
            '*' => Http::response([
                'call_id' => $callId,
                'message' => 'ok',
            ]),
        ]);

        $callback = tap($assignment->actualCallback()->fill(['call_at' => now(), 'called_at' => now()]))->save();
        $this->assertNotNull($callback->called_at);

        $this->get(route('crm.assignment.call-alt', $assignment));

        $this->assertDatabaseCount('callbacks', 2);
    }
}
