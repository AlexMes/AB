<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configure test suite
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setGuard('api');
    }

    /** @test */
    public function user_should_be_authenticated_to_update_passwords()
    {
        $this->assertGuest('api');

        $this->putJson(route('api.users.reset-password', factory(User::class)->create()), [
            'password'              => '123456789',
            'password_confirmation' => '123456789',
        ])->assertStatus(401);
    }

    /** @test */
    public function password_length_should_be_at_least_5_chars()
    {
        $this->signIn();

        $this->putJson(route('api.users.reset-password', factory(User::class)->create()), [
            'password'              => '1234',
            'password_confirmation' => '1234',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function password_should_be_confirmed()
    {
        $this->signIn();

        $this->putJson(route('api.users.reset-password', factory(User::class)->create()), [
            'password'              => '123456',
            'password_confirmation' => '1234567',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
