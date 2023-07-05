<?php

namespace App\Policies;

use App\ManualPour;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualPourPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isDeveloper()) {
            return false;
        }

        if ($user->isDesigner()) {
            return false;
        }

        if ($user->isFinancier()) {
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
     * @param \App\ManualPour $manualPour
     *
     * @return mixed
     */
    public function view(User $user, ManualPour $manualPour)
    {
        return $user->is($manualPour->user);
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
     * @param \App\ManualPour $manualPour
     *
     * @return mixed
     */
    public function update(User $user, ManualPour $manualPour)
    {
        return $user->is($manualPour->user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User       $user
     * @param \App\ManualPour $manualPour
     *
     * @return mixed
     */
    public function delete(User $user, ManualPour $manualPour)
    {
        return false;
    }
}
