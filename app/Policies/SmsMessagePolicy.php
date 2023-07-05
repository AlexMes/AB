<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SmsMessagePolicy
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

        if ($user->isSales()) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function view()
    {
        return true;
    }
}
