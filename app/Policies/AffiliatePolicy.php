<?php

namespace App\Policies;

use App\Affiliate;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AffiliatePolicy
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
        return $user->isAdmin() || $user->isSupport() || $user->isSales() || $user->isTeamLead()
            || $user->isBranchHead();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User      $user
     * @param \App\Affiliate $affiliate
     *
     * @return mixed
     */
    public function view(User $user, Affiliate $affiliate)
    {
        if ($user->isBranchHead() || $user->isSupport()) {
            return $user->branch_id === $affiliate->branch_id;
        }

        return $user->isAdmin();
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
        return $user->isAdmin() || $user->isSupport() || $user->isBranchHead();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User      $user
     * @param \App\Affiliate $affiliate
     *
     * @return mixed
     */
    public function update(User $user, Affiliate $affiliate)
    {
        if ($user->isBranchHead() || $user->isSupport()) {
            return $user->branch_id === $affiliate->branch_id;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User      $user
     * @param \App\Affiliate $affiliate
     *
     * @return mixed
     */
    public function delete(User $user, Affiliate $affiliate)
    {
        return $user->isAdmin();
    }
}
