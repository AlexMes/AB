<?php

namespace App\Policies;

use App\OfficeStatus;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficeStatusPolicy
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
     * @param \App\User         $user
     * @param \App\OfficeStatus $officeStatus
     *
     * @return mixed
     */
    public function view(User $user, OfficeStatus $officeStatus)
    {
        return $user->isDeveloper();
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
        return $user->isDeveloper();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User         $user
     * @param \App\OfficeStatus $officeStatus
     *
     * @return mixed
     */
    public function update(User $user, OfficeStatus $officeStatus)
    {
        return $user->isDeveloper();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User         $user
     * @param \App\OfficeStatus $officeStatus
     *
     * @return mixed
     */
    public function delete(User $user, OfficeStatus $officeStatus)
    {
        return false;
    }
}
