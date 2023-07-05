<?php

namespace App\Http\Controllers;

use App\Team;
use App\User;
use Illuminate\Http\Request;

class TeamUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Team $team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team->users()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Team                     $team
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Team $team, Request $request)
    {
        $this->authorize('update', $team);

        $user = User::findOrFail($request->input('user_id'));

        if ($team->users()->where('user_id', $user->id)->doesntExist()) {
            $team->users()->attach($user);
        }

        return response()->json($user, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Team $team
     * @param User      $user
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, User $user)
    {
        $this->authorize('update', $team);

        $team->users()->detach($user);

        return response()->noContent();
    }
}
