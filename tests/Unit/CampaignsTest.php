<?php

namespace Tests\Unit;

use App\Facebook\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function itSetsCampaignUtmKeyOnSave()
    {
        $campaign  = new Campaign(['id' => '1234','account_id' => '1234', 'name' => 'something-campaign-check']);

        $campaign->save();

        $this->assertEquals('check', $campaign->utm_key);
    }
}
