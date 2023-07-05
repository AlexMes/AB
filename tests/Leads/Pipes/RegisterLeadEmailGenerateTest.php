<?php

namespace Tests\Leads\Pipes;

use App\IpAddress;
use App\Jobs\AssignMarkersToLead;
use App\Lead;
use App\LeadMarker;
use App\Leads\Pipes\GenerateEmail;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RegisterLeadEmailGenerateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ifLeadEmailNotNull()
    {
        $lead = factory(Lead::class)->create([
            'email' => 'test@gmail.com'
        ]);

        $pipe = new GenerateEmail();

        $pipe->handle($lead, function ($lead) {
            $this->assertEquals('test@gmail.com', $lead->email);
        });
    }

    /** @test */
    public function ifLeadEmailNull()
    {
        $lead = factory(Lead::class)->create([
            'email' => null
        ]);

        $pipe = new GenerateEmail();

        $object = $pipe->handle($lead, function ($lead) {
            $this->assertNotNull($lead->email);

            return $lead;
        });

        $this->assertTrue(Cache::get('ge-' . $object->email));

        AssignMarkersToLead::dispatchNow($lead);

        $marker = LeadMarker::where('lead_id', $object->id)->where('name', 'email-generated')->first();

        $this->assertEquals($object->id, $marker->lead_id);
    }

    /** @test */
    public function ifLeadEmailNullAndLeadCountryRu()
    {
        $ip = '127.0.0.1';

        $lead = factory(Lead::class)->create([
            'ip'    => $ip,
            'email' => null
        ]);

        IpAddress::create([
            'ip'           => $ip,
            'country_code' => 'RU',
        ]);

        $pipe = new GenerateEmail();

        $object = $pipe->handle($lead, function ($lead) {
            $this->assertNotNull($lead->email);

            return $lead;
        });

        $this->assertTrue(Cache::get('ge-' . $object->email));

        AssignMarkersToLead::dispatchNow($lead);

        $marker = LeadMarker::where('lead_id', $object->id)->where('name', 'email-generated')->first();

        $this->assertEquals($object->id, $marker->lead_id);
    }

    /** @test */
    public function ifOfferGenerateEmailFalse()
    {
        $offer = factory(Offer::class)->create([
            'generate_email' => false
        ]);

        $lead = factory(Lead::class)->create([
            'email'    => null,
            'offer_id' => $offer->id
        ]);

        $pipe = new GenerateEmail();

        $object = $pipe->handle($lead, function ($lead) {
            $this->assertFalse($lead->offer->generate_email);

            return $lead;
        });

        $this->assertNull($object->email);
    }

    /** @test */
    public function ifOfferGenerateEmailTrue()
    {
        $offer = factory(Offer::class)->create();

        $lead = factory(Lead::class)->create([
            'email'    => null,
            'offer_id' => $offer->id
        ]);

        $pipe = new GenerateEmail();

        $object = $pipe->handle($lead, function ($lead) {
            $this->assertTrue($lead->offer->generate_email);

            return $lead;
        });

        $this->assertNotNull($object->email);
    }
}
