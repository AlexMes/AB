<?php

namespace Tests;

use App\User;

/**
 * All of the interactions with auth during test run
 * should live here.
 */
trait InteractsWithAuth
{
    /**
     * Default auth guard for tests
     * Using api as app supposed to be mostly SPA.
     *
     * @var string
     */
    protected $guard = 'api';

    /**
     * Currently authenticated user
     *
     * @var \App\User;
     */
    protected $user = null;

    /**
    * Set authentication guard
    *
    * @param string $guard
    *
    * @return $this
    */
    protected function setGuard(string $guard)
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Sign in as user.
     *
     * @param null|\App\User $user
     *
     * @return $this
     */
    protected function signIn($user = null)
    {
        $this->actingAs(
            $user ?? $user = factory(User::class)->create(),
            $this->guard
        );

        $this->user = $user;

        $this->assertAuthenticatedAs($user, $this->guard);

        return $this;
    }


    /**
     * Get currently authenticated user
     *
     * @return \App\User
     */
    protected function getUser()
    {
        return $this->user;
    }
}
