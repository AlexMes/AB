<?php

namespace App\Deluge\Policies;

use App\Deluge\Domain;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isDeveloper()) {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->branch_id === 19;
    }

    /**
     * @return bool
     */
    public function view(User $user, Domain $domain)
    {
        if ($user->isBranchHead()) {
            return $domain->user->branch->is($user->branch);
        }

        return $domain->user->is($user);
    }

    public function create(User $user)
    {
        return $user->branch_id === 19;
    }

    public function update(User $user, Domain $domain)
    {
        if ($user->isBranchHead()) {
            return $domain->user->branch->is($user->branch);
        }

        return $domain->user->is($user);
    }
}
