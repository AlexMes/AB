<?php

namespace App\CRM\Policies;

use App\CRM\Label;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class LabelPolicy
{
    use HandlesAuthorization;

    /**
     * Determines is user allowed to see
     * labels at all
     *
     * @param Authenticatable $user
     *
     * @return bool
     */
    public function viewAny(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Authenticatable $user
     * @param Label           $label
     *
     * @return mixed
     */
    public function view(Authenticatable $user, Label $label)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        return false;
    }

    /**
     * Determine is user allowed to add
     * label to database
     *
     * @param Authenticatable $user
     *
     * @return bool
     */
    public function create(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        return false;
    }

    /**
     * Determine is user allowed to update
     * label details in the database
     *
     * @param Authenticatable $user
     * @param Label           $label
     *
     * @return bool
     */
    public function update(Authenticatable $user, Label $label)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        return false;
    }

    /**
     * Determine is user allowed to delete
     * label from the database
     *
     * @param Authenticatable $user
     * @param Label           $label
     *
     * @return bool
     */
    public function delete(Authenticatable $user, Label $label)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        return false;
    }
}
