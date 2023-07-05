<?php

namespace Tests\Unit;

use App\Facebook\Ad;
use App\Facebook\Events\Ads\Created;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AdTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDispatchesCreatedEvent()
    {
        Event::fake();

        $ad = factory(Ad::class)->create();


        Event::assertDispatched(Created::class, fn ($event) => $event->ad->is($ad));
    }
}
