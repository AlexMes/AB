<?php

namespace App\Policies;

use App\LeadPaymentCondition;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPaymentConditionPolicy
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
        return $user->hasRole([User::SUPPORT]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User                 $user
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return mixed
     */
    public function view(User $user, LeadPaymentCondition $leadPaymentCondition)
    {
        return $user->hasRole([User::SUPPORT]);
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
        return $user->hasRole([User::SUPPORT]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User                 $user
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return mixed
     */
    public function update(User $user, LeadPaymentCondition $leadPaymentCondition)
    {
        return $user->hasRole([User::SUPPORT]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User                 $user
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return mixed
     */
    public function delete(User $user, LeadPaymentCondition $leadPaymentCondition)
    {
        return $user->hasRole([User::SUPPORT]);
    }
}
