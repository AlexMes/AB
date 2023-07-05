<?php

namespace App\Policies;

use App\Facebook\Campaign;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isFarmer()) {
            return false;
        }

        if ($user->isFinancier()) {
            return false;
        }

        if ($user->isDeveloper()) {
            return false;
        }

        if ($user->isSales()) {
            return false;
        }
    }

    /**
     * Determine when user is able to view campaigns list
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * Determine when user can view campaign details
     *
     * @param \App\User              $user
     * @param \App\Facebook\Campaign $campaign
     *
     * @return bool
     */
    public function view(User $user, Campaign $campaign)
    {
        return $user->accounts->contains($campaign->account);
    }

    /**
     * Determine when user can create new campaign
     *
     * @return bool
     */
    public function store()
    {
        return true;
    }

    /**
     * Determine when user can update campaign
     *
     * @param \App\User              $user
     * @param \App\Facebook\Campaign $campaign
     *
     * @return bool
     */
    public function update(User $user, Campaign $campaign)
    {
        return $user->accounts->contains($campaign->account);
    }

    /**
     * Determine when user can destroy campaign
     *
     * @param \App\User              $user
     * @param \App\Facebook\Campaign $campaign
     *
     * @return bool
     */
    public function destroy(User $user, Campaign $campaign)
    {
        return $user->accounts->contains($campaign->account);
    }

    /**
     * Determine when user is able to mass stop campaigns
     *
     * @return bool
     */
    public function massStop()
    {
        return true;
    }
}
