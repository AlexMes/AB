<?php

namespace App\Policies;

use App\OfficePayment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficePaymentPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User          $user
     * @param \App\OfficePayment $officePayment
     *
     * @return mixed
     */
    public function view(User $user, OfficePayment $officePayment)
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
        return $user->isSupport();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User          $user
     * @param \App\OfficePayment $officePayment
     *
     * @return mixed
     */
    public function update(User $user, OfficePayment $officePayment)
    {
        return $user->isSupport();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User          $user
     * @param \App\OfficePayment $officePayment
     *
     * @return mixed
     */
    public function delete(User $user, OfficePayment $officePayment)
    {
        return false;
    }
}
