<?php

namespace App\Http\Controllers\Users;

use App\Firewall as UserFirewall;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Firewall\Create;
use App\User;

class Firewall extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user)
    {
        $this->authorize('view', $user);

        return response()->json($user->firewall, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param User   $user
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user, Create $request)
    {
        $this->authorize('update', $user);

        $user->firewall()->updateOrCreate(['ip' => $request->input('ip')], $request->validated());

        return response()->json($user->fresh(['firewall'])->firewall, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Firewall            $permission
     *
     * @return \Illuminate\Http\Response
     */
    /*public function update(Request $request, UserPermission $permission)
    {
        //
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param User          $user
     * @param \App\Firewall $firewall
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user, UserFirewall $firewall)
    {
        $firewall->delete();

        return response()->json($user->firewall, 202);
    }
}
