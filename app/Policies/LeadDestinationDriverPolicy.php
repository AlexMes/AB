<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadDestinationDriverPolicy
{
    use HandlesAuthorization;

    public function showConfiguration(User $user): bool
    {
        if ($user->isSupport() || $user->isAdmin()) {
            return true;
        }

        return false;
    }
}
