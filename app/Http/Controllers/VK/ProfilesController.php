<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Http\Requests\VKProfiles\Update;
use App\VK\Models\Profile;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Profile::class);
    }

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
            ->with(['user'])
            ->orderByDesc('id');

        if ($request->has('all')) {
            return $profiles->get(['id','name','user_id']);
        }

        return $profiles->paginate();
    }

    /**
     * Load details for single registered account
     *
     * @param \App\VK\Models\Profile $profile
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \App\VK\Models\Profile
     */
    public function show(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile->load('user');
    }

    /**
     * Update profile information
     *
     * @param \App\VK\Models\Profile               $profile
     * @param \App\Http\Requests\VKProfiles\Update $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Profile $profile, Update $request)
    {
        return response(tap($profile)->update($request->validated())->loadMissing('user'), 202);
    }
}
