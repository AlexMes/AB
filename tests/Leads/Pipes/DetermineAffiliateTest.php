<?php

namespace Leads\Pipes;

use App\Affiliate;
use App\Lead;
use App\Leads\Pipes\DetermineAffiliate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetermineAffiliateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDeterminesAffiliateFromApiKey()
    {
        $affiliate = factory(Affiliate::class)->create();

        $lead = new Lead(['api_key' => $affiliate->api_key]);

        $pipe = new DetermineAffiliate();

        $pipe->handle($lead, function ($lead) use ($affiliate) {
            $this->assertNotNull($lead->affiliate_id, 'Affiliate is not detected');
            $this->assertEquals($affiliate->id, $lead->affiliate_id, 'Wrong affiliate was detected');
            $this->assertEquals($affiliate->offer_id, $lead->offer_id, 'Offer was not set from affiliate');
        });
    }

    /** @test */
    public function itRemoveApiKeyAttributeFromLead()
    {
        $lead = new Lead(['api_key' => 'Some key']);
        $this->assertNotNull($lead->api_key, 'Api key was not set on lead.');

        $pipe = new DetermineAffiliate();

        $pipe->handle($lead, function ($lead) {
            $this->assertNull($lead->api_key, 'API key was not removed from the lead');
            $this->assertObjectNotHasAttribute('api_key', $lead, 'Property wasnt removed from model');
        });
    }
}
