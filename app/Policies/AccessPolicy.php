<?php

namespace App\Policies;

use App\Access;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isVerifier()) {
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
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * @param \App\User   $user
     * @param \App\Access $access
     *
     * @return mixed
     */
    public function view(User $user, Access $access)
    {
        return $user->is($access->user) || $user->isFarmer();
    }

    /**
     * Determine when user can create new access
     *
     * @return bool
     */
    public function create()
    {
        return true;
    }

    /**
     * Determine when user can update access
     *
     * @param \App\User   $user
     * @param \App\Access $access
     *
     * @return bool
     */
    public function update(User $user, Access $access)
    {
        return $user->is($access->user) || $user->isFarmer();
    }

    /**
     * Determine when user can delete access
     *
     * @param \App\User $user
     *
     * @return bool
     */
    public function destroy(User $user)
    {
        return $user->isAdmin();
    }
}
