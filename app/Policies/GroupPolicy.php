<?php

namespace App\Policies;

use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
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
     * Determine whether the user can view any groups.
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
     * Determine whether the user can view the group.
     *
     * @param \App\User  $user
     * @param \App\Group $group
     *
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        if ($user->isFarmer()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isFarmer()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param \App\User  $user
     * @param \App\Group $group
     *
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        if ($user->isFarmer()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param \App\User  $user
     * @param \App\Group $group
     *
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return false;
    }
}
