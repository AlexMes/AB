<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;

class Adsets extends Controller
{
    /**
     * Get all related adsets
     *
     * @param \App\Facebook\Profile $profile
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->adsets()
            ->visible()
            ->with('account')
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderBy('spend')
            ->get();
    }
}
