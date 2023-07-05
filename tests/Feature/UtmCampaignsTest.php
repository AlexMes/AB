<?php

namespace Tests\Feature;

use App\Facebook\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UtmCampaignsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuth()
    {
        $this->assertGuest('api');

        $this->getJson(route('utm-campaigns.index'))
            ->assertStatus(401);
    }

    /** @test */
    public function itRespondsOk()
    {
        $this->signIn()->getJson(route('utm-campaigns.index'))->assertOk();
    }


    /** @test */
    public function itLoadsOnlyDistinctUtmCampaigns()
    {
        Event::fake();
        factory(Campaign::class, 2)->create(['name' => 'some-one','utm_key' => 'one','offer_id' => 1]);
        factory(Campaign::class, 2)->create(['name' => 'some-two','utm_key' => 'two','offer_id' => 2]);

        $this->signIn()->getJson(route('utm-campaigns.index'))
            ->assertJsonCount(2);
    }
}
