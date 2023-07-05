<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function requiresAuth()
    {
        $this->assertGuest();

        $this->getJson(route('me'))->assertStatus(401);
    }

    /** @test */
    public function respondsWithCurrentUserData()
    {
        $this->signIn();

        $this->getJson(route('me'))->assertJson([
            'name'  => $this->getUser()->name,
            'email' => $this->getUser()->email,
            'role'  => $this->getUser()->role,
        ]);
    }
}
