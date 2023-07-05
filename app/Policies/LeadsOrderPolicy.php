<?php

namespace App\Policies;

use App\LeadsOrder;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadsOrderPolicy
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

    public function view(User $user, LeadsOrder $order)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            if (!is_null($order->branch_id)) {
                return $user->branch_id === $order->branch_id;
            }

            return $user->can('view', $order->office);
        }

        if ($user->isDeveloper()) {
            return $order->date->greaterThanOrEqualTo(now()->subMonths(2));
        }

        if ($user->isSales()) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT,User::DEVELOPER])) {
            return true;
        }

        return false;
    }

    public function update(User $user, LeadsOrder $order)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT,User::DEVELOPER])) {
            if (!is_null($order->branch_id)) {
                return $user->branch_id === $order->branch_id;
            }

            return $user->can('view', $order->office);
        }

        return $user->office->is($order->office);
    }

    public function destroy()
    {
        return false;
    }
}
