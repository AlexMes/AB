<?php

namespace App\Integrations\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayloadPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine when user can create new access
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine when user can update access
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine when user can delete access
     *
     * @param User $user
     *
     * @return bool
     */
    public function destroy(User $user)
    {
        return $user->isAdmin();
    }
}
