<?php

namespace App\Policies;

use App\ProfileLog;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfileLogPolicy
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
     * @param \App\User       $user
     * @param \App\ProfileLog $profileLog
     *
     * @return mixed
     */
    public function view(User $user, ProfileLog $profileLog)
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
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User       $user
     * @param \App\ProfileLog $profileLog
     *
     * @return mixed
     */
    public function update(User $user, ProfileLog $profileLog)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User       $user
     * @param \App\ProfileLog $profileLog
     *
     * @return mixed
     */
    public function delete(User $user, ProfileLog $profileLog)
    {
        return false;
    }
}
