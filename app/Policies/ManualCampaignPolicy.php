<?php

namespace App\Policies;

use App\ManualCampaign;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualCampaignPolicy
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
     * @param \App\User           $user
     * @param \App\ManualCampaign $manualCampaign
     *
     * @return mixed
     */
    public function view(User $user, ManualCampaign $manualCampaign)
    {
        if (!$user->allowedOffers->contains($manualCampaign->offer)) {
            return false;
        }

        if ($manualCampaign->account->user_id === null) {
            return true;
        }

        return $manualCampaign->account->user->is($user)
            || in_array($manualCampaign->account->user_id, User::visible()->pluck('id')->toArray());
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
     * @param \App\User           $user
     * @param \App\ManualCampaign $manualCampaign
     *
     * @return mixed
     */
    public function update(User $user, ManualCampaign $manualCampaign)
    {
        if (!$user->allowedOffers->contains($manualCampaign->offer)) {
            return false;
        }

        if ($manualCampaign->account->user_id === null) {
            return true;
        }

        return $manualCampaign->account->user->is($user)
            || in_array($manualCampaign->account->user_id, User::visible()->pluck('id')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User           $user
     * @param \App\ManualCampaign $manualCampaign
     *
     * @return mixed
     */
    public function delete(User $user, ManualCampaign $manualCampaign)
    {
        return false;
    }
}
