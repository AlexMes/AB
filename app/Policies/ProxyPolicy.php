<?php

namespace App\Policies;

use App\Proxy;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProxyPolicy
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
        return $user->hasRole([User::DEVELOPER, User::SUPPORT]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User  $user
     * @param \App\Proxy $proxy
     *
     * @return mixed
     */
    public function view(User $user, Proxy $proxy)
    {
        return $user->hasRole([User::DEVELOPER, User::SUPPORT]);
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
        return $user->hasRole([User::DEVELOPER, User::SUPPORT]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User  $user
     * @param \App\Proxy $proxy
     *
     * @return mixed
     */
    public function update(User $user, Proxy $proxy)
    {
        return $user->hasRole([User::DEVELOPER, User::SUPPORT]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User  $user
     * @param \App\Proxy $proxy
     *
     * @return mixed
     */
    public function delete(User $user, Proxy $proxy)
    {
        return $user->hasRole([User::DEVELOPER, User::SUPPORT]);
    }
}
