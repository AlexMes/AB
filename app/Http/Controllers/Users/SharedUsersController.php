<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateSharedUser;
use App\Http\Requests\Users\DeleteSharedUser;
use App\User;

class SharedUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $this->authorize('view', $user);

        return response()->json($user->sharedUsers()->orderByDesc('id')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Users\CreateSharedUser $request
     * @param User                                      $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateSharedUser $request, User $user)
    {
        $sharedUser = User::findOrFail($request->input('shared_id'));

        $user->sharedUsers()->syncWithoutDetaching($request->input('shared_id'));

        return response()->json($sharedUser, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteSharedUser $request
     * @param User             $user
     * @param User             $sharedUser
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(DeleteSharedUser $request, User $user, User $sharedUser)
    {
        $user->sharedUsers()->detach($sharedUser);

        return response()->json($sharedUser, 202);
    }
}
