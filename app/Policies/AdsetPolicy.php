<?php

namespace App\Policies;

use App\Facebook\AdSet;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdsetPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isFinancier()) {
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
     * Determine when user is able to view adsets list
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    public function update(User $user, AdSet $adSet)
    {
        return $user->accounts->contains($adSet->account);
    }

    /**
     * Determine when user is able to mass stop adsets
     *
     * @return bool
     */
    public function massStop()
    {
        return true;
    }
}
