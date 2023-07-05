<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Lead;
use App\User;

class Leads extends Controller
{
    /**
     * Leads attached to user
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function __invoke(User $user)
    {
        $this->authorize('viewAny', Lead::class);

        return $user->leads()
            ->visible()
            ->allowedOffers()
            ->with('offer')
            ->paginate();
    }
}
