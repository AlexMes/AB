<?php

namespace App\Policies;

use App\Result;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultPolicy
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
     * Determine is user can view any result
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * Determine when user can view result details
     *
     * @param \App\User $user
     *
     * @return bool
     */
    public function show(User $user)
    {
        return true;
    }

    /**
     * Determine when user can store new results
     *
     * @return bool
     */
    public function store()
    {
        return true;
    }

    /**
     * Determine when user can update result
     *
     * @return bool
     */
    public function update()
    {
        return false;
    }

    /**
     * Determine when user can delete result
     *
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
