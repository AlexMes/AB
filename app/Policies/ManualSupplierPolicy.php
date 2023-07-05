<?php

namespace App\Policies;

use App\ManualSupplier;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualSupplierPolicy
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
        return $user->isBranchHead() || $user->isDeveloper();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User           $user
     * @param \App\ManualSupplier $manualSupplier
     *
     * @return mixed
     */
    public function view(User $user, ManualSupplier $manualSupplier)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        if ($user->isBranchHead()) {
            return $user->branch_id === $manualSupplier->branch_id || $manualSupplier->branch_id === null;
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
        return $user->isBranchHead() || $user->isDeveloper();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User           $user
     * @param \App\ManualSupplier $manualSupplier
     *
     * @return mixed
     */
    public function update(User $user, ManualSupplier $manualSupplier)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        if ($user->isBranchHead()) {
            return $user->branch_id === $manualSupplier->branch_id || $manualSupplier->branch_id === null;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User           $user
     * @param \App\ManualSupplier $manualSupplier
     *
     * @return mixed
     */
    public function delete(User $user, ManualSupplier $manualSupplier)
    {
        return false;
    }
}
