<?php

namespace App\Gamble\Policies;

use App\Gamble\Campaign;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User            $user
     * @param \App\Gamble\Campaign $campaign
     *
     * @return mixed
     */
    public function view(User $user, Campaign $campaign)
    {
        if ($user->isGamblerAdmin()) {
            return true;
        }

        if ($campaign->account->user_id === null) {
            return true;
        }

        return $user->is($campaign->account->user);
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
     * @param \App\User            $user
     * @param \App\Gamble\Campaign $campaign
     *
     * @return mixed
     */
    public function update(User $user, Campaign $campaign)
    {
        if ($user->isGamblerAdmin()) {
            return true;
        }

        if ($campaign->account->user_id === null) {
            return true;
        }

        return $user->is($campaign->account->user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User            $user
     * @param \App\Gamble\Campaign $campaign
     *
     * @return mixed
     */
    public function delete(User $user, Campaign $campaign)
    {
        return false;
    }
}
