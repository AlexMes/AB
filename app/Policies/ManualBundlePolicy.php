<?php

namespace App\Policies;

use App\ManualBundle;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualBundlePolicy
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
        return $user->isDeveloper()
            || $user->isTeamLead()
            || $user->isBranchHead();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User         $user
     * @param \App\ManualBundle $manualBundle
     *
     * @return mixed
     */
    public function view(User $user, ManualBundle $manualBundle)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        return $user->allowedOffers->contains($manualBundle->offer);
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
        return $user->hasRole([User::ADMIN,User::DEVELOPER,User::HEAD,User::TEAMLEAD]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User         $user
     * @param \App\ManualBundle $manualBundle
     *
     * @return mixed
     */
    public function update(User $user, ManualBundle $manualBundle)
    {
        if ($user->isDeveloper()) {
            return true;
        }

        return $user->allowedOffers->contains($manualBundle->offer);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User         $user
     * @param \App\ManualBundle $manualBundle
     *
     * @return mixed
     */
    public function delete(User $user, ManualBundle $manualBundle)
    {
        return false;
    }
}
