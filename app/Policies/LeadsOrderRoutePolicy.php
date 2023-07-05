<?php

namespace App\Policies;

use App\LeadOrderRoute;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadsOrderRoutePolicy
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
    }

    public function viewAny(User $user)
    {
        return $user->isCustomer()
            || $user->isSupport()
            || $user->isDeveloper()
            || $user->isSubSupport()
            || $user->isSales()
            || $user->isBranchHead();
    }

    public function view(User $user, LeadOrderRoute $route)
    {
        if ($user->isSupport() || $user->isSubSupport()) {
            return true;
        }

        if ($user->isDeveloper()) {
            return $user->can('view', $route->order);
        }

        return false;
    }

    public function create(User $user)
    {
        if ($user->isSupport() || $user->isSubSupport()) {
            return true;
        }

        if ($user->isDeveloper()) {
            return true;
        }

        if ($user->isBranchHead()) {
            return true;
        }

        return false;
    }

    public function update(User $user, LeadOrderRoute $route)
    {
        if ($user->isSupport() || $user->isSubSupport()) {
            return true;
        }

        if ($user->isDeveloper()) {
            return $user->can('update', $route->order);
        }

        if ($user->isBranchHead()) {
            return $user->can('update', $route->order);
        }

        if ($user->isSales()) {
            return false;
        }

        return $route->order->office->is($user->office);
    }

    public function delete(User $user, LeadOrderRoute $route)
    {
        if ($user->isSupport()) {
            return true;
        }

        if ($user->isDeveloper()) {
            return $route->order->date->greaterThanOrEqualTo(now()->subMonths(2));
        }

        if ($user->isSales()) {
            return false;
        }

        return $route->order->office->is($user->office);
    }

    /**
     * Determine whether the user can restore the route.
     *
     * @param \App\User      $user
     * @param LeadOrderRoute $route
     *
     * @return mixed
     */
    public function restore(User $user, LeadOrderRoute $route)
    {
        if ($user->isSales()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can mass start routes
     *
     * @param User $user
     *
     * @return bool
     */
    public function massStart(User $user)
    {
        return $user->isCustomer()
            || $user->isSupport()
            || $user->isDeveloper()
            || $user->isSubSupport()
            || $user->isBranchHead();
    }

    /**
     * Determine if the user can mass pause routes
     *
     * @param User $user
     *
     * @return bool
     */
    public function massPause(User $user)
    {
        return $user->isCustomer()
            || $user->isSupport()
            || $user->isDeveloper()
            || $user->isSubSupport()
            || $user->isBranchHead();
    }

    /**
     * Determine if the user can mass stop routes
     *
     * @param User $user
     *
     * @return bool
     */
    public function massStop(User $user)
    {
        return $user->isCustomer()
            || $user->isSupport()
            || $user->isDeveloper()
            || $user->isSubSupport()
            || $user->isBranchHead();
    }
}
