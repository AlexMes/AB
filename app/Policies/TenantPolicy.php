<?php

namespace App\Policies;

use App\CRM\Tenant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
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
        return $user->isSupport() || $user->isDeveloper();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User       $user
     * @param \App\CRM\Tenant $tenant
     *
     * @return mixed
     */
    public function view(User $user, Tenant $tenant)
    {
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User       $user
     * @param \App\CRM\Tenant $tenant
     *
     * @return mixed
     */
    public function update(User $user, Tenant $tenant)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User       $user
     * @param \App\CRM\Tenant $tenant
     *
     * @return mixed
     */
    public function delete(User $user, Tenant $tenant)
    {
        return false;
    }
}
