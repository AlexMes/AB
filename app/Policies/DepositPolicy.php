<?php

namespace App\Policies;

use App\Deposit;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Carbon;

class DepositPolicy
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
    }

    /**
     * Determine when user can index deposits
     *
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user->isAdmin()
            || $user->isBranchHead()
            || $user->isSupport()
            || $user->isDeveloper()
            || $user->isSubSupport()
            || $user->isSales()) {
            return true;
        }

        return false;
    }

    /**
     * Determine when user can view deposit
     *
     * @param \App\User    $user
     * @param \App\Deposit $deposit
     *
     * @return bool
     */
    public function view(User $user, Deposit $deposit)
    {
        if ($user->isSupport()) {
            return Carbon::parse($deposit->date)->greaterThanOrEqualTo(now()->subMonth()->startOfMonth());
        }

        if ($user->isDeveloper()) {
            return Carbon::parse($deposit->date)->isSameMonth(now());
        }

        if (!$user->allowedOffers->contains($deposit->offer)) {
            return false;
        }

        if ($user->isBuyer()) {
            return false;
        }

        return $user->is($deposit->user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isSupport() || $user->isDeveloper() || $user->isSubSupport();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user, Deposit $deposit)
    {
        if ($user->isSupport()) {
            return Carbon::parse($deposit->date)->greaterThanOrEqualTo(now()->subMonth()->startOfMonth());
        }

        if ($user->isDeveloper()) {
            return Carbon::parse($deposit->date)->isSameMonth(now());
        }

        return false;
    }

    /**
     * @param User    $user
     * @param Deposit $deposit
     *
     * @return bool
     */
    public function delete(User $user, Deposit $deposit)
    {
        if ($user->hasRole([User::ADMIN,User::SUPPORT]) && $user->branch_id === 16) {
            return $user->branch_id === $deposit->offer->branch_id;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
