<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\ProfilePage;
use App\Http\Controllers\Controller;

/**
 * Class FacebookComments
 *
 * @package App\Http\Controllers\Profiles
 */
class FacebookComments extends Controller
{
    /**
     * @param ProfilePage $page
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(ProfilePage $page)
    {
        $this->authorize('view', $page);

        return $page->comments()
            ->with('account')
            ->get();
    }
}
