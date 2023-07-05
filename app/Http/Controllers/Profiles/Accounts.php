<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;

class Accounts extends Controller
{
    /**
     * Get all related ad accounts for singe account
     *
     * @param \App\Facebook\Profile $profile
     *
     * @return \App\Facebook\Account[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->accounts()
            ->visible()
            ->with('group')
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderBy('spend')
            ->get();
    }
}
