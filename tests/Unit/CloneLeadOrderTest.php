<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit;

use App\Jobs\CloneLeadOrder;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CloneLeadOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configure test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create();
        $this->signIn($user);
    }

    /** @test */
    public function itClonesLeadOrderToSpecificDate()
    {
        $this->seed(\LeadsOrderWithRoutes::class);

        $order = LeadsOrder::first();

        $this->assertNotNull($order);

        // Clone leads order to tomorrow.
        $order->cloneToDate(now()->addDay());

        $clone = LeadsOrder::whereDate('date', now()->addDay())->first();

        // Ensure clone is created
        $this->assertNotNull($clone, 'Leads order was not cloned');
        // Ensure date is correct
        $this->assertEquals(
            $clone->date->toDateString(),
            now()->addDay()->toDateString(),
            'Order cloned to wrong date'
        );
        // Ensure office is correct
        $this->assertEquals(
            $order->office_id,
            $clone->office_id,
            'Order cloned to wrong office'
        );
    }

    /** @test */
    public function itDispatchesCloningJob()
    {
        Queue::fake();

        $order = factory(LeadsOrder::class)->create();

        $order->cloneToDate(now()->addDay());

        Queue::assertPushed(
            CloneLeadOrder::class,
            fn ($job) => $job->order->is($order) && $job->date == now()->addDay()
        );
    }

    /** @test */
    public function itClonesAllRoutesDefinedInOriginalOrder()
    {
        $this->seed(\LeadsOrderWithRoutes::class);
        $order = LeadsOrder::first();
        $this->assertNotNull($order);
        $order->cloneToDate(now()->addDay());
        /** @var \Illuminate\Database\Eloquent\Collection | LeadsOrder $clone */
        $clone = LeadsOrder::whereDate('date', now()->addDay())->first();


        $this->assertEquals($order->routes()->count(), $clone->routes()->count());
    }

    /** @test */
    public function itClonesAllRoutesExactlyAsTheyAreDefinedInOriginalOrder()
    {
        $this->seed(\LeadsOrderWithRoutes::class);
        $order = LeadsOrder::first();
        $this->assertNotNull($order);
        $order->cloneToDate(now()->addDay());
        $clone = LeadsOrder::whereDate('date', now()->addDay())->first();

        foreach ($order->routes as $route) {
            $this->assertTrue(
                $clone->routes()
                    ->where('offer_id', $route->offer_id)
                    ->where('manager_id', $route->manager_id)
                    ->where('leadsOrdered', $route->leadsOrdered)
                    ->exists()
            );
        }
    }

    /** @test */
    public function itClonesDiffRoutesIfOrderExists()
    {
        $this->seed(\LeadsOrderWithRoutes::class);
        /** @var LeadsOrder $order */
        $order = LeadsOrder::first();
        $this->assertNotNull($order);
        /** @var LeadOrderRoute $route */
        $route = $order->routes()->first();

        /** @var LeadsOrder $clone */
        $clone = factory(LeadsOrder::class)->create(['date' => now()->addDay(), 'office_id' => $order->office_id]);
        $clone->routes()->create([
            'offer_id'     => $route->offer_id,
            'manager_id'   => $route->manager_id,
            'leadsOrdered' => $route->leadsOrdered,
        ]);
        $this->assertCount(1, $clone->routes()->get());

        $order->cloneToDate(now()->addDay());
        /** @var \Illuminate\Database\Eloquent\Collection | LeadsOrder $clone */
        $clone = LeadsOrder::whereDate('date', now()->addDay())->first();


        $this->assertEquals($order->routes()->count(), $clone->routes()->count());
    }
}
