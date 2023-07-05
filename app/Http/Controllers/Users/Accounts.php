<?php

namespace App\Http\Controllers\Users;

use App\Facebook\Account;
use App\Http\Controllers\Controller;
use App\User;

class Accounts extends Controller
{
    /**
     * Get user assigned accounts
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function __invoke(User $user)
    {
        $this->authorize('viewAny', Account::class);

        return $user->accounts()
            ->visible()
            ->with(['profile', 'group'])
            ->paginate();
    }
}
