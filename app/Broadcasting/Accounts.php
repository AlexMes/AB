<?php

namespace App\Broadcasting;

use App\Facebook\Account;
use App\User;

class Accounts
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User    $user
     * @param Account $account
     *
     * @return array|bool
     */
    public function join(User $user, Account $account)
    {
        return $user->can('view', $account);
    }
}
