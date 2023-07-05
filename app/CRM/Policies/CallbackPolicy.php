<?php

namespace App\CRM\Policies;

use App\CRM\Callback;
use App\Manager;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class CallbackPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param Authenticatable $user
     *
     * @return mixed
     */
    public function viewAny(Authenticatable $user)
    {
        if ($user instanceof User) {
            return true;
        }

        /** @var Manager $user */
        if ($user->isOfficeHead()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Authenticatable   $user
     * @param \App\CRM\Callback $callback
     *
     * @return mixed
     */
    public function view(Authenticatable $user, Callback $callback)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param Authenticatable $user
     *
     * @return mixed
     */
    public function create(Authenticatable $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param Authenticatable   $user
     * @param \App\CRM\Callback $callback
     *
     * @return mixed
     */
    public function update(Authenticatable $user, Callback $callback)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param Authenticatable   $user
     * @param \App\CRM\Callback $callback
     *
     * @return mixed
     */
    public function delete(Authenticatable $user, Callback $callback)
    {
        return false;
    }
}
