<?php

namespace App\Http\Controllers\Profiles;

use App\Facebook\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Update;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    /**
     * List all registered FB accounts
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $profiles =  Profile::query()
            ->visible()
            ->searchIn(['name'], $request->get('search'))
            ->notEmptyWhereIn('user_id', $request->get('users'))
            ->with(['user', 'pages', 'group', 'app'])
            ->when($request->get('active'), fn ($q) => $q->issueDoesntExist())
            ->orderByDesc('id');

        if ($request->has('all')) {
            return $profiles->get(['id','name','user_id']);
        }

        return $profiles->paginate();
    }

    /**
     * Load details for single registered account
     *
     * @param \App\Facebook\Profile $profile
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \App\Facebook\Profile
     */
    public function show(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->load('user', 'group');
    }

    /**
     * Update profile information
     *
     * @param \App\Facebook\Profile              $profile
     * @param \App\Http\Requests\Profiles\Update $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Profile $profile, Update $request)
    {
        return response(tap($profile->loadMissing('user', 'group'))->update($request->validated()), 202);
    }

    /**
     * Destroy the profile
     *
     * @param Profile $profile
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        $this->authorize('destroy', $profile);

        $profile->remove();

        return response()->noContent();
    }
}
