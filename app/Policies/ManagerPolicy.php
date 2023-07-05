<?php

namespace App\Policies;

use App\Manager;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManagerPolicy
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
     * @param \App\User    $user
     * @param \App\Manager $manager
     *
     * @return mixed
     */
    public function view(User $user, Manager $manager)
    {
        return true;
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
        return $user->isAdmin() || $user->isSupport() || $user->isSubSupport();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User    $user
     * @param \App\Manager $manager
     *
     * @return mixed
     */
    public function update(User $user, Manager $manager)
    {
        return $user->isAdmin() || $user->isSupport() || $user->isSubSupport();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User    $user
     * @param \App\Manager $manager
     *
     * @return mixed
     */
    public function destroy(User $user, Manager $manager)
    {
        return $user->isAdmin() || $user->isSupport() || $user->isSubSupport();
    }
}
