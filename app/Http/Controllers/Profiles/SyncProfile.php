<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;

class SyncProfile extends Controller
{
    /**
     * Run profile synchronization
     *
     * @param \App\Facebook\Profile $profile
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Profile $profile)
    {
        $profile->refreshFacebookData(true);

        return response('Running', 200);
    }
}
