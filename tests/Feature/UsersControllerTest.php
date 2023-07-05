<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuthToIndexUsers()
    {
        $this->assertGuest();

        $this->getJson(route('users.index'))
            ->assertStatus(401)
            ->assertUnauthorized();
    }

    /** @test */
    public function itPaginatesUsers()
    {
        $this->signIn();

        factory(User::class, 20)->create();

        $this->getJson(route('users.index'))
            ->assertStatus(200)
            ->assertJsonCount(15, 'data');
    }


    /** @test */
    public function itRequiresAuthToLoadSingleUserInfo()
    {
        $this->assertGuest();

        $user = factory(User::class)->create();

        $this->getjson(route('users.show', $user->id))->assertStatus(401);
    }

    /** @test */
    public function itAllowsLoadingSingleUserInformation()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->getJson(route('users.show', $user->id))
            ->assertStatus(200)
            ->assertJson($user->toArray());
    }

    /** @test */
    public function itAllowsUsersToCreateOtherUsers()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'role'                  => User::BUYER,
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]))
            ->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function nameIsRequiredToCreateUser()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => null,
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function emailIsRequiredToCreateUser()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => 'test',
            'email'                 => null,
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function emailMustBeUniqueToCreateUser()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => 'test',
            'email'                 => $this->getUser()->email,
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
    /** @test */
    public function passwordIsRequiredToCreateUser()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => 'test',
            'email'                 => 'test@example.com',
            'password'              => null,
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function passwordMustBeConfirmedToCreateUser()
    {
        $this->signIn();

        $this->postJson(route('users.store', $user = [
            'name'                  => 'test',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password-whatever'
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function itAllowsUsersToUpdateOwnProfiles()
    {
        $this->signIn();

        $this->putJson(route('users.update', $this->getUser()->id), [
            'name'  => 'NewName',
            'email' => 'new@email.com'
        ])
            ->assertStatus(202);

        $this->assertDatabaseHas('users', ['email' => 'new@email.com']);
    }

    /** @test */
    public function nameIsRequiredToUpdateUser()
    {
        $this->signIn();

        $this->putJson(route('users.update', $this->getUser()->id), [
            'name'  => null,
            'email' => 'new@email.com'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function emailIsRequiredToUpdateUser()
    {
        $this->signIn();

        $this->putJson(route('users.update', $this->getUser()->id), [
            'name'  => 'test',
            'email' => null
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function emailMustStayUniqueToUpdateUser()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->putJson(route('users.update', $this->getUser()->id), [
            'name'  => 'test',
            'email' => $user->email
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function currentUserEmailIsIgnoredDuringUpdate()
    {
        $this->signIn();


        $this->putJson(route('users.update', $this->getUser()->id), [
            'name'  => 'test',
            'email' => $this->getUser()->email
        ])
            ->assertStatus(202)
            ->assertJsonMissingValidationErrors('email');
    }

    /** @test */
    public function itNotDisallowsUserDeletion()
    {
        $this->signIn();
        $user = factory(User::class)->create();

        $this->deleteJson(route('users.destroy', $user->id))
            ->assertStatus(403);
    }
}
