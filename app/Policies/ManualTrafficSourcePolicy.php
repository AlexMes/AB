<?php

namespace App\Policies;

use App\ManualTrafficSource;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualTrafficSourcePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isBranchHead()) {
            return true;
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
     * @param \App\User                $user
     * @param \App\ManualTrafficSource $trafficSource
     *
     * @return mixed
     */
    public function view()
    {
        return true;
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User                $user
     * @param \App\ManualTrafficSource $trafficSource
     *
     * @return mixed
     */
    public function update(User $user, ManualTrafficSource $trafficSource)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User                $user
     * @param \App\ManualTrafficSource $trafficSource
     *
     * @return mixed
     */
    public function delete(User $user, ManualTrafficSource $trafficSource)
    {
        return false;
    }
}
