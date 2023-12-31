<?php

namespace App\Policies;

use App\UnityOrganization;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnityOrganizationPolicy
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
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User              $user
     * @param \App\UnityOrganization $unityOrganization
     *
     * @return mixed
     */
    public function view(User $user, UnityOrganization $unityOrganization)
    {
        return $user->branch_id === 16;
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
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User              $user
     * @param \App\UnityOrganization $unityOrganization
     *
     * @return mixed
     */
    public function update(User $user, UnityOrganization $unityOrganization)
    {
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User              $user
     * @param \App\UnityOrganization $unityOrganization
     *
     * @return mixed
     */
    public function delete(User $user, UnityOrganization $unityOrganization)
    {
        return false;
    }
}
