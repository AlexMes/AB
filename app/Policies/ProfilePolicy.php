<?php

namespace App\Policies;

use App\Facebook\Profile;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isFinancier()) {
            return false;
        }

        if ($user->isDeveloper()) {
            return false;
        }

        if ($user->isSales()) {
            return false;
        }
    }

    /**
     * Determine when user is able to view profiles list
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * Determine is user can view profile
     *
     * @param \App\User             $user
     * @param \App\Facebook\Profile $profile
     *
     * @return bool
     */
    public function view(User $user, Profile $profile)
    {
        if ($user->isVerifier()) {
            return $profile->user_id === $user->id;
        }

        if ($profile->user_id === null) {
            return true;
        }

        if ($user->isFarmer()) {
            return true;
        }

        return $user->is($profile->user);
    }

    /**
     * @param \App\User             $user
     * @param \App\Facebook\Profile $profile
     *
     * @return bool
     */
    public function update(User $user, Profile $profile)
    {
        if ($user->isVerifier()) {
            return false;
        }

        if ($profile->user_id === null) {
            return true;
        }

        if ($user->isFarmer()) {
            return true;
        }

        return $user->is($profile->user);
    }

    /**
     * @param User    $user
     * @param Profile $profile
     *
     * @return bool
     */
    public function destroy(User $user, Profile $profile)
    {
        if ($user->isVerifier()) {
            return $profile->user_id === $user->id;
        }

        return false;
    }
}
