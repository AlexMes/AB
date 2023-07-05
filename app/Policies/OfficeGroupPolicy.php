<?php

namespace App\Policies;

use App\OfficeGroup;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficeGroupPolicy
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
        return $user->hasRole([User::SUPPORT, User::HEAD, User::DEVELOPER, User::TEAMLEAD]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User        $user
     * @param \App\OfficeGroup $officeGroup
     *
     * @return mixed
     */
    public function view(User $user, OfficeGroup $officeGroup)
    {
        if ($user->hasRole([User::SUPPORT, User::HEAD, User::DEVELOPER])) {
            return $user->branch_id === $officeGroup->branch_id;
        }

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
        return $user->hasRole([User::SUPPORT, User::HEAD, User::DEVELOPER]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User        $user
     * @param \App\OfficeGroup $officeGroup
     *
     * @return mixed
     */
    public function update(User $user, OfficeGroup $officeGroup)
    {
        if ($user->hasRole([User::SUPPORT, User::HEAD, User::DEVELOPER])) {
            return $user->branch_id === $officeGroup->branch_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User        $user
     * @param \App\OfficeGroup $officeGroup
     *
     * @return mixed
     */
    public function delete(User $user, OfficeGroup $officeGroup)
    {
        return false;
    }
}
