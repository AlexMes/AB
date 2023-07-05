<?php

namespace App\Policies;

use App\Facebook\ProfilePage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePagePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isFarmer()) {
            return false;
        }

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
     * Determine whether the user can view any profile pages.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->isVerifier()) {
            return true;
        }

        return $user !== null;
    }

    /**
     * Determine whether the user can view the profile page.
     *
     * @param \App\User                 $user
     * @param \App\Facebook\ProfilePage $profilePage
     *
     * @return mixed
     */
    public function view(User $user, ProfilePage $profilePage)
    {
        if ($user->isVerifier()) {
            return $profilePage->profile->user_id === $user->id;
        }

        return $user !== null;
    }

    /**
     * Determine whether the user can create profile pages.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the profile page.
     *
     * @param \App\User                     $user
     * @param \App\App\Facebook\ProfilePage $profilePage
     *
     * @return mixed
     */
    public function update(User $user, ProfilePage $profilePage)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the profile page.
     *
     * @param \App\User                     $user
     * @param \App\App\Facebook\ProfilePage $profilePage
     *
     * @return mixed
     */
    public function delete(User $user, ProfilePage $profilePage)
    {
        return false;
    }
}
