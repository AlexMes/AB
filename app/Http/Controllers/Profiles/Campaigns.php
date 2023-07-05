<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;

class Campaigns extends Controller
{
    /**
     * Get all related campaigns for single related FB Account
     *
     * @param \App\Facebook\Profile $profile
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->campaigns()
            ->visible()
            ->with(['account'])
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderBy('spend')
            ->get();
    }
}
