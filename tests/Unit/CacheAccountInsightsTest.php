<?php


namespace Tests\Unit;

use App\AdsBoard;
use App\Facebook\Account;
use App\Facebook\Jobs\CacheInsights;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CacheAccountInsightsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDispatchesCachingJob()
    {
        \Queue::fake();
        $a = factory(Account::class)->make();
        $a->cacheInsights(now());

        \Queue::assertPushed(CacheInsights::class);
    }

    /** @test */
    public function itUsesRightQueue()
    {
        \Queue::fake();
        $a = factory(Account::class)->create();
        $a->cacheInsights(now());

        \Queue::assertPushedOn(AdsBoard::QUEUE_INSIGHTS, CacheInsights::class);
    }
}
