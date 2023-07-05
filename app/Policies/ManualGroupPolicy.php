<?php

namespace App\Policies;

use App\ManualGroup;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualGroupPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
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
        if ($user->isBuyer()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User        $user
     * @param \App\ManualGroup $manualGroup
     *
     * @return mixed
     */
    public function view(User $user, ManualGroup $manualGroup)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        if ($user->hasRole([User::TEAMLEAD, User::HEAD])) {
            return $user->branch_id === $manualGroup->branch_id || $manualGroup->branch_id === null;
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
        return $user->isBranchHead() || $user->isTeamLead() || $user->isDeveloper();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User        $user
     * @param \App\ManualGroup $manualGroup
     *
     * @return mixed
     */
    public function update(User $user, ManualGroup $manualGroup)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        if ($user->hasRole([User::TEAMLEAD, User::HEAD])) {
            return $user->branch_id === $manualGroup->branch_id || $manualGroup->branch_id === null;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User        $user
     * @param \App\ManualGroup $manualGroup
     *
     * @return mixed
     */
    public function delete(User $user, ManualGroup $manualGroup)
    {
        return false;
    }
}
