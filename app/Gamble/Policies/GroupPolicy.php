<?php

namespace App\Gamble\Policies;

use App\Gamble\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
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
     * @param \App\User         $user
     * @param \App\Gamble\Group $group
     *
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->isGamblerAdmin();
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
        return $user->isGamblerAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User         $user
     * @param \App\Gamble\Group $group
     *
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->isGamblerAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User         $user
     * @param \App\Gamble\Group $group
     *
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return false;
    }
}
