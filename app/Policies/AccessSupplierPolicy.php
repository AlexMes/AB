<?php

namespace App\Policies;

use App\AccessSupplier;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessSupplierPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isSales()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any access suppliers.
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
     * Determine whether the user can view the access supplier.
     *
     * @param \App\User           $user
     * @param \App\AccessSupplier $accessSupplier
     *
     * @return mixed
     */
    public function view(User $user, AccessSupplier $accessSupplier)
    {
        return false;
    }

    /**
     * Determine whether the user can create access suppliers.
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
     * Determine whether the user can update the access supplier.
     *
     * @param \App\User           $user
     * @param \App\AccessSupplier $accessSupplier
     *
     * @return mixed
     */
    public function update(User $user, AccessSupplier $accessSupplier)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the access supplier.
     *
     * @param \App\User           $user
     * @param \App\AccessSupplier $accessSupplier
     *
     * @return mixed
     */
    public function delete(User $user, AccessSupplier $accessSupplier)
    {
        return false;
    }
}
