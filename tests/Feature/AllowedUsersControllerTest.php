<?php

namespace Tests\Feature;

use App\Events\OfferAllowed;
use App\Offer;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AllowedUsersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDispatchesAllowedOfferEventOnCreate()
    {
        Event::fake();
        $user  = factory(User::class)->state('admin')->create();
        $offer = factory(Offer::class)->create();
        $this->signIn(factory(User::class)->state('admin')->create());

        $this->postJson(route('api.allowed-users.store', $offer), ['user_id' => $user->id])
            ->assertStatus(201);

        Event::assertDispatched(OfferAllowed::class, function (OfferAllowed $event) use ($user, $offer) {
            return $event->user->is($user) && $event->offer->is($offer);
        });
    }
}
