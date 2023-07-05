<?php

namespace App\Policies\VK;

use App\User;
use App\VK\Models\Profile;
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
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User              $user
     * @param \App\VK\Models\Profile $profile
     *
     * @return \Illuminate\Auth\Access\Response|bool
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
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User              $user
     * @param \App\VK\Models\Profile $profile
     *
     * @return \Illuminate\Auth\Access\Response|bool
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
     * Determine whether the user can delete the model.
     *
     * @param \App\User              $user
     * @param \App\VK\Models\Profile $profile
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Profile $profile)
    {
        if ($user->isVerifier()) {
            return $profile->user_id === $user->id;
        }

        return false;
    }
}
