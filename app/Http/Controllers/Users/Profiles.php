<?php

namespace App\Http\Controllers\Users;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;
use App\User;

class Profiles extends Controller
{
    /**
     * Get user assigned profiles
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function __invoke(User $user)
    {
        $this->authorize('viewAny', Profile::class);

        return $user->profiles()
            ->visible()
            ->latest()
            ->paginate();
    }
}
