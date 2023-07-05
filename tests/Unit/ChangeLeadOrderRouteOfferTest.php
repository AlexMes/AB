<?php

namespace Tests\Unit;

use App\Jobs\ChangeLeadOrderRouteOffer;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ChangeLeadOrderRouteOfferTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_change_offer_job()
    {
        Queue::fake();
        Event::fake();

        $route = factory(LeadOrderRoute::class)->create();
        $offer = factory(Offer::class)->create();

        $route->changeOffer($offer);

        Queue::assertPushed(
            ChangeLeadOrderRouteOffer::class,
            fn ($job) => $job->route->is($route) && $job->offer->is($offer),
        );
    }

    /** @test */
    public function it_creates_route_for_target_offer_in_current_order_when_required()
    {
        Event::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create();

        /** @var Offer $offer */
        $offer = factory(Offer::class)->create();

        $route->changeOffer($offer);

        $this->assertTrue(
            $offer->routes()
                ->where('order_id', $route->order_id)
                ->where('manager_id', $route->manager_id)
                ->exists()
        );
    }

    /** @test */
    public function it_reuse_route_if_it_exists_in_the_offer_of_current_order()
    {
        Event::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create();

        /** @var Offer $offer */
        $offer = factory(Offer::class)->create();

        $offer->routes()->create([
            'order_id'   => $route->order_id,
            'manager_id' => $route->manager_id
        ]);

        $route->changeOffer($offer);

        $this->assertEquals(1, $offer->routes()->count());
    }

    /** @test */
    public function it_transfers_diff_amount_to_the_target_offer()
    {
        Event::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create([
            'leadsOrdered'  => 10,
            'leadsReceived' => 7,
        ]);

        /** @var LeadOrderRoute $targetRoute */
        $targetRoute = factory(LeadOrderRoute::class)->create([
            'order_id'      => $route->order_id,
            'manager_id'    => $route->manager_id,
            'leadsOrdered'  => 1,
            'leadsReceived' => 1,
        ]);

        $route->changeOffer($targetRoute->offer);

        $this->assertEquals(7, $route->refresh()->leadsOrdered);
        $this->assertEquals(4, $targetRoute->refresh()->leadsOrdered);
    }

    /** @test */
    public function it_does_not_change_leads_received_amount_in_both_routes()
    {
        Event::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create([
            'leadsOrdered'  => 10,
            'leadsReceived' => 7,
        ]);

        /** @var LeadOrderRoute $targetRoute */
        $targetRoute = factory(LeadOrderRoute::class)->create([
            'order_id'      => $route->order_id,
            'manager_id'    => $route->manager_id,
            'leadsOrdered'  => 1,
            'leadsReceived' => 1,
        ]);

        $route->changeOffer($targetRoute->offer);

        $this->assertEquals(7, $route->refresh()->leadsReceived);
        $this->assertEquals(1, $targetRoute->refresh()->leadsReceived);
    }
}
