<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TraffiSourcePolicy
 *
 * @package App\Policies
 */
class TrafficSourcePolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\User $user
     *
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole([User::SUPPORT,User::SUBSUPPORT,User::DEVELOPER,User::HEAD,User::ADMIN]);
    }

    /**
     * @param \App\User $user
     *
     * @return bool
     */
    public function view(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     *
     * @return bool
     */
    public function update(User $user)
    {
        return $user->isAdmin();
    }
}
