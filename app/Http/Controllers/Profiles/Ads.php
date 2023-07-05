<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;

class Ads extends Controller
{
    /**
     * Get all profile ads
     *
     * @param \App\Facebook\Profile $profile
     *
     * @return \App\Facebook\Ad[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->ads()
            ->visible()
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderBy('spend')
            ->get();
    }
}
