<?php

namespace App\Policies;

use App\Bundle;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BundlePolicy
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

        if ($user->isDesigner()) {
            return false;
        }

        if ($user->isVerifier()) {
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
     * Determine whether the user can view any bundles.
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
     * Determine whether the user can view the bundle.
     *
     * @param \App\User   $user
     * @param \App\Bundle $bundle
     *
     * @return mixed
     */
    public function view(User $user, Bundle $bundle)
    {
        return true;
    }

    /**
     * Determine whether the user can create bundles.
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
     * Determine whether the user can update the bundle.
     *
     * @param \App\User   $user
     * @param \App\Bundle $bundle
     *
     * @return mixed
     */
    public function update(User $user, Bundle $bundle)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the bundle.
     *
     * @param \App\User   $user
     * @param \App\Bundle $bundle
     *
     * @return mixed
     */
    public function delete(User $user, Bundle $bundle)
    {
        return false;
    }
}
