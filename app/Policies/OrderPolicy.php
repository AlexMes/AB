<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
     * Determine when user can index orders
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * Determine when user can view order
     *
     * @return bool
     */
    public function view()
    {
        return true;
    }

    /**
     * Determine when user can create order
     *
     * @return bool
     */
    public function store()
    {
        return false;
    }

    /**
     * Determine when user can update order
     *
     * @return bool
     */
    public function update()
    {
        return false;
    }

    /**
     * Determine when user can delete order
     *
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
