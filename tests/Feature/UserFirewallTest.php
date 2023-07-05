<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFirewallTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function itStoresFirewallRules()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->postJson(route('api.firewall.store', $user), [
            'ip' => '127.0.0.1',
        ])->assertStatus(201);

        $this->assertTrue($user->refresh()->firewall->contains('ip', '=', '127.0.0.1'));
    }

    /** @test */
    public function itRequiresAuth()
    {
        $this->assertGuest();

        $user = factory(User::class)->create();

        $this->postJson(route('api.firewall.store', $user))->assertStatus(401);

        $this->assertEmpty($user->refresh()->firewall);
    }

    /** @test */
    public function itRequiresIpAddress()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->postJson(route('api.firewall.store', $user), [
            'ip' => null,
        ])->assertStatus(422)->assertJsonValidationErrors('ip');

        $this->assertEmpty($user->refresh()->firewall);
    }

    /** @test */
    public function itRequiresIpAddressToBeValid()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->postJson(route('api.firewall.store', $user), [
            'ip' => 'not-ip-address',
        ])->assertStatus(422)->assertJsonValidationErrors('ip');
    }
}
