<?php

namespace Leads;

use App\Lead;
use App\LeadOrderRoute;
use App\Leads\DetermineRoute;
use App\LeadsOrder;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PresetLeadAssignments;
use Tests\TestCase;

class DetermineRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function itRespondsWithNullWhenRoutesNotFound()
    {
        $lead = factory(Lead::class)->create();

        $this->assertNull(LeadOrderRoute::whereOfferId($lead->offer_id)->first());

        $route = (new DetermineRoute($lead))->get();

        $this->assertNull($route, 'Found route, when should not');
    }

    /** @test */
    public function itRespondsWithRouteWhenFoundOne()
    {
        $this->seed(PresetLeadAssignments::class);
        $lead  = factory(Lead::class)->create(['offer_id' => Offer::first()->id]);
        $route = new DetermineRoute($lead);

        $this->assertNotNull($route->get(), 'Blind piece of shit');
        $this->assertInstanceOf(LeadOrderRoute::class, $route->get());
    }

    /** @test */
    public function routeOfferMatchesWithAskedOne()
    {
        $this->seed(PresetLeadAssignments::class);
        $lead = factory(Lead::class)->create(['offer_id' => Offer::first()->id]);

        $route = new DetermineRoute($lead);

        $this->assertEquals(Offer::first()->id, $route->get()->offer_id);
    }

    /** @test */
    public function itRespondsOnlyWithIncompleteRoutes()
    {
        $this->seed(PresetLeadAssignments::class);
        $lead  = factory(Lead::class)->create(['offer_id' => Offer::first()->id]);
        $route = (new DetermineRoute($lead))->get();

        $this->assertTrue($route->isIncomplete(), 'Returns completed route');
        $this->assertGreaterThan($route->leadsReceived, $route->leadsOrdered);
    }

    /** @test */
    public function itIsTakingOnlyCurrentOrders()
    {
        $this->seed(PresetLeadAssignments::class);
        $lead = factory(Lead::class)->create(['offer_id' => Offer::first()->id]);

        LeadsOrder::first()->update([
            'date' => now()->subDay()
        ]);
        $route = (new DetermineRoute($lead))->get();

        $this->assertNull($route, 'Returns route from order for different date');
    }

    /** @test */
    public function itRespondsWithNullWhenShouldSkip()
    {
        $this->seed(PresetLeadAssignments::class);
        $lead  = factory(Lead::class)->create(['offer_id' => Offer::first()->id]);
        $route = (new DetermineRoute($lead))->skipRoutes([LeadOrderRoute::pluck('id')->values()]);

        $this->assertNull($route->get());
    }
}
