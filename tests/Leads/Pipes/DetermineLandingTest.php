<?php

namespace Leads\Pipes;

use App\Domain;
use App\Lead;
use App\Leads\Pipes\DetermineLanding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetermineLandingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDetectsLanding()
    {
        $landing = factory(Domain::class)->state('landing')->create(['url' => 'https://example.com']);

        $lead   = new Lead(['domain' => 'example.com']);

        $pipe = new DetermineLanding();

        $pipe->handle($lead, function ($lead) use ($landing) {
            $this->assertNotNull($lead->landing_id, 'Landing not detected at all');
            $this->assertEquals($landing->id, $lead->landing_id, 'Landing is not detected properly');
            $this->assertEquals($landing->offer_id, $lead->offer_id, 'Offer was not set from landing');
        });
    }

    /** @test */
    public function itDetectsLandingWhenDomainDoesNotContainsSchema()
    {
        $domain = factory(Domain::class)->state('landing')->create(['url' => 'example.com']);

        $lead   = new Lead(['domain' => 'example.com']);

        $pipe = new DetermineLanding();

        $pipe->handle($lead, function ($lead) use ($domain) {
            $this->assertNotNull($lead->landing_id, 'Landing not detected at all');
            $this->assertEquals($domain->id, $lead->landing_id, 'Landing is not detected properly');
        });
    }

    /** @test */
    public function itSetLandingIdToNullWhenNoDomainFound()
    {
        $lead = new Lead(['domain' => 'example.com']);

        $pipe = new DetermineLanding();

        $pipe->handle($lead, function ($lead) {
            $this->assertNull($lead->landing_id, 'Landing must be empty, but doesnt.');
        });
    }
}
