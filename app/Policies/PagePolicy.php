<?php

namespace App\Policies;

use App\Page;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isVerifier()) {
            return false;
        }

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
     * @param \App\User $user
     * @param \App\Page $page
     *
     * @return mixed
     */
    public function view(User $user, Page $page)
    {
        if ($user->isDeveloper()) {
            return false;
        }

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
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\Page $page
     *
     * @return mixed
     */
    public function update(User $user, Page $page)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\Page $page
     *
     * @return mixed
     */
    public function delete(User $user, Page $page)
    {
        return $user->isAdmin();
    }
}
