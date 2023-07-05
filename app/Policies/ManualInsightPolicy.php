<?php

namespace App\Policies;

use App\ManualInsight;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualInsightPolicy
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
     * @param \App\User          $user
     * @param \App\ManualInsight $manualInsight
     *
     * @return mixed
     */
    public function view(User $user, ManualInsight $manualInsight)
    {
        if (!$user->allowedOffers->contains($manualInsight->campaign->offer)) {
            return false;
        }

        if ($manualInsight->account->user_id === null) {
            return true;
        }

        return $manualInsight->account->user->is($user);
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
     * @param \App\User          $user
     * @param \App\ManualInsight $manualInsight
     *
     * @return mixed
     */
    public function update(User $user, ManualInsight $manualInsight)
    {
        if (!$user->allowedOffers->contains($manualInsight->campaign->offer)) {
            return false;
        }

        if ($manualInsight->account->user_id === null) {
            return true;
        }

        return $manualInsight->account->user->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User          $user
     * @param \App\ManualInsight $manualInsight
     *
     * @return mixed
     */
    public function delete(User $user, ManualInsight $manualInsight)
    {
        return false;
    }
}
