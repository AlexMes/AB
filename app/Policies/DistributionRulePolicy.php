<?php

namespace App\Policies;

use App\DistributionRule;
use App\Offer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DistributionRulePolicy
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
        return $user->hasRole([User::SUPPORT, User::DEVELOPER]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User             $user
     * @param \App\DistributionRule $distributionRule
     *
     * @return mixed
     */
    public function view(User $user, DistributionRule $distributionRule)
    {
        if ($user->hasRole([User::SUPPORT, User::DEVELOPER])) {
            return Offer::allowed()->whereId($distributionRule->offer_id)->exists();
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
        return $user->hasRole([User::SUPPORT, User::DEVELOPER]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User             $user
     * @param \App\DistributionRule $distributionRule
     *
     * @return mixed
     */
    public function update(User $user, DistributionRule $distributionRule)
    {
        if ($user->hasRole([User::SUPPORT, User::DEVELOPER])) {
            return Offer::allowed()->whereId($distributionRule->offer_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User             $user
     * @param \App\DistributionRule $distributionRule
     *
     * @return mixed
     */
    public function delete(User $user, DistributionRule $distributionRule)
    {
        return $user->hasRole([User::SUPPORT, User::DEVELOPER]);
    }
}
