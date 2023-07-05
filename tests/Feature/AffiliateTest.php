<?php

namespace Tests\Feature;

use App\Affiliate;
use App\Lead;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function itCreatesTokenOnCreation()
    {
        $affiliate = Affiliate::create(['name' => 'Yolo']);

        $this->assertNotNull($affiliate->api_key);
    }

    /** @test */
    public function itDetectsAffiliateTokenOnLeadCreation()
    {
        $this->withoutExceptionHandling();

        $affiliate = Affiliate::create(['name' => 'Yolo','offer_id' => factory(Offer::class)->create()->id]);

        $this->postJson(route('leads.external'), [
            'firstname' => 'Yo',
            'phone'     => '89845934585345',
            'api_key'   => $affiliate->api_key,
        ])->assertStatus(201);


        $this->assertEquals(Lead::first()->affiliate_id, $affiliate->id);
    }

    /** @test */
    public function leadWithoutAffiliateIsOk()
    {
        $this->withoutExceptionHandling();

        $affiliate = Affiliate::create(['name' => 'Yolo']);

        $this->postJson(route('leads.external'), [
            'firstname' => 'Yo',
            'phone'     => '89845934585345',
        ])->assertStatus(201);


        $this->assertNull(Lead::first()->affiliate_id);
    }

    /** @test */
    public function wrongApiKeyIsOk()
    {
        $this->postJson(route('leads.external'), [
            'firstname' => 'Yo',
            'phone'     => '89845934585345',
            'api_key'   => 'asdsfajnfijbgn',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('api_key');


        $this->assertNull(Lead::first());
    }
}
