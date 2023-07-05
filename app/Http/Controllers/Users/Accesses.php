<?php


namespace App\Http\Controllers\Users;

use App\Access;
use App\Http\Controllers\Controller;
use App\User;

class Accesses extends Controller
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
        $this->authorize('viewAny', Access::class);

        return $user->accesses()
            ->visible()
            ->with(['supplier'])
            ->paginate();
    }
}
