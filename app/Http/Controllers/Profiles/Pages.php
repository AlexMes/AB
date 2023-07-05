<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Facebook\ProfilePage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Pages extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Profile                  $profile
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Profile $profile, Request $request)
    {
        $this->authorize('viewAny', ProfilePage::class);

        return $profile->pages()
            ->visible()
            ->searchIn(['name'], $request->search)
            ->get(['id', 'name']);
    }
}
