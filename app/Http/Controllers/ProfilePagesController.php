<?php

namespace App\Http\Controllers;

use App\Facebook\ProfilePage;
use Illuminate\Http\Request;

class ProfilePagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProfilePage::class);

        return ProfilePage::query()
            ->visible()
            ->notEmptyWhereIn('profile_id', $request->profile_id)
            ->searchIn(['name'], $request->search)
            ->with('profile')
            ->paginate();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Facebook\ProfilePage $profilePage
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return ProfilePage
     */
    public function show(ProfilePage $profilePage)
    {
        $this->authorize('view', $profilePage);

        return $profilePage->load('profile');
    }
}
