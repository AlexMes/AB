<?php

namespace App\Gamble\Policies;

use App\Gamble\GoogleAppLink;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoogleAppLinkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User                 $user
     * @param \App\Gamble\GoogleAppLink $googleAppLink
     *
     * @return mixed
     */
    public function view(User $user, GoogleAppLink $googleAppLink)
    {
        if ($user->isGamblerAdmin()) {
            return true;
        }

        return $user->is($googleAppLink->user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User                 $user
     * @param \App\Gamble\GoogleAppLink $googleAppLink
     *
     * @return mixed
     */
    public function update(User $user, GoogleAppLink $googleAppLink)
    {
        if ($user->isGamblerAdmin()) {
            return true;
        }

        return $user->is($googleAppLink->user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User                 $user
     * @param \App\Gamble\GoogleAppLink $googleAppLink
     *
     * @return mixed
     */
    public function delete(User $user, GoogleAppLink $googleAppLink)
    {
        return false;
    }
}
