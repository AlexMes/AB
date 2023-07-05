<?php

namespace App\Policies;

use App\Domain;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainsPolicy
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
     * Determine is given user able to view domain
     *
     * @param User $user
     *
     * @return bool
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->isDesigner()) {
            return false;
        }

        return true;
    }

    /**
     * @param User   $user
     * @param Domain $domain
     *
     * @return bool
     */
    public function update(User $user, Domain $domain)
    {
        if ($user->isDesigner()) {
            return false;
        }

        if ($user->isTeamLead() && ($domain->user_id === null || $user->inTeammates($domain->user_id))) {
            return true;
        }

        if ($user->isDeveloper()) {
            return true;
        }

        return $user->is($domain->user);
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
