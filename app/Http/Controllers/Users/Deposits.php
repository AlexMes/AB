<?php


namespace App\Http\Controllers\Users;

use App\Deposit;
use App\Http\Controllers\Controller;
use App\User;

class Deposits extends Controller
{
    /**
     * User deposits
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function __invoke(User $user)
    {
        $this->authorize('viewAny', Deposit::class);

        return $user->deposits()
            ->visible()
            ->allowedOffers()
            ->with('account', 'office', 'offer', 'lead')
            ->paginate();
    }
}
