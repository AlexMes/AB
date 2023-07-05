<?php

namespace Tests\Unit;

use App\Facebook\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignUtmSettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itSetsUtmKeyOnCreate()
    {
        $campaign = factory(Campaign::class)->make();

        $campaign->name = 'someshit-campaign-test';

        $campaign->save();

        $this->assertEquals('test', $campaign->utm_key);
    }

    /** @test */
    public function itSetsUtmKeyOnUpdate()
    {
        $campaign = factory(Campaign::class)->create();
        $this->assertNotEquals('test', $campaign->utm_key);

        $campaign->name = 'someshit-campaign-test';
        $campaign->save();
        $this->assertEquals('test', $campaign->utm_key);
    }
}
