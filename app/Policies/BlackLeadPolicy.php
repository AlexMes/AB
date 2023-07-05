<?php

namespace App\Policies;

use App\BlackLead;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlackLeadPolicy
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
     * @param \App\User      $user
     * @param \App\BlackLead $blackLead
     *
     * @return mixed
     */
    public function view(User $user, BlackLead $blackLead)
    {
        if ($user->isDeveloper()) {
            return true;
        }

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
        if ($user->isDeveloper()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User      $user
     * @param \App\BlackLead $blackLead
     *
     * @return mixed
     */
    public function update(User $user, BlackLead $blackLead)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User      $user
     * @param \App\BlackLead $blackLead
     *
     * @return mixed
     */
    public function delete(User $user, BlackLead $blackLead)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        return false;
    }
}
