<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * @param \App\User $auth
     *
     * @return bool
     */
    public function view(User $auth, User $user)
    {
        if ($auth->isFarmer()) {
            return false;
        }

        if ($auth->isFinancier()) {
            return false;
        }

        if ($auth->isVerifier()) {
            return false;
        }

        if ($auth->isSales()) {
            return false;
        }

        return true;
    }

    /**
     * @param \App\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isDeveloper();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user)
    {
        return $user->isDeveloper();
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
