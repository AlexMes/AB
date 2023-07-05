<?php

namespace App\Policies;

use App\Facebook\Account;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
     * Determine is user able to view any accounts
     *
     * @return bool
     */
    public function viewAny()
    {
        return true;
    }

    /**
     * Determine is user able to view account
     *
     * @param \App\User             $user
     * @param \App\Facebook\Account $account
     *
     * @return bool
     */
    public function view(User $user, Account $account)
    {
        return $user->can('view', $account->profile);
    }

    /**
     * Determine is user able to update account info
     *
     * @param \App\User             $user
     * @param \App\Facebook\Account $account
     *
     * @return bool
     */
    public function update(User $user, Account $account)
    {
        return $user->can('update', $account->profile);
    }
}
