<?php

namespace App\Policies;

use App\Facebook\FacebookApp;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FacebookAppPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isSales()) {
            return false;
        }
    }

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
     * @param \App\Facebook\FacebookApp $facebookApp
     *
     * @return mixed
     */
    public function view(User $user, FacebookApp $facebookApp)
    {
        return false;
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
        if ($user->isVerifier()) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User                 $user
     * @param \App\Facebook\FacebookApp $facebookApp
     *
     * @return mixed
     */
    public function update(User $user, FacebookApp $facebookApp)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User                 $user
     * @param \App\Facebook\FacebookApp $facebookApp
     *
     * @return mixed
     */
    public function delete(User $user, FacebookApp $facebookApp)
    {
        return false;
    }
}
