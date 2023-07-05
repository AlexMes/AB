<?php

namespace Tests\Feature;

use App\Facebook\AdSet;
use App\Facebook\Commands\StartCampaignsAdsets;
use App\Facebook\Jobs\StartAdset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StartCampaignsAdsetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRunsAdsetStarts()
    {
        Queue::fake();

        $adset = factory(AdSet::class)->create([
            'start_midnight' => true,
        ]);

        $this->artisan(StartCampaignsAdsets::class);

        Queue::assertPushed(StartAdset::class, fn ($job) => $job->adset->is($adset));
    }

    /** @test */
    public function itRemovesMarks()
    {
        Queue::fake();

        $adset = factory(AdSet::class)->create([
            'start_midnight' => true,
        ]);

        $this->artisan(StartCampaignsAdsets::class);

        Queue::assertPushed(StartAdset::class, fn ($job) => $job->adset->is($adset));

        $this->assertFalse($adset->fresh()->start_midnight);
    }
}
