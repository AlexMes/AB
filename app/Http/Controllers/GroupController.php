<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Groups\Create;
use App\Http\Requests\Groups\Update;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Group::class);

        return Group::query()
            ->searchIn('name', $request->search)
            ->unless($request->has('all'), function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $this->authorize('create', Group::class);

        return response()->json(Group::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Group $group
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $this->authorize('view', $group);

        return response()->json($group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update     $request
     * @param \App\Group $group
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Group $group)
    {
        $this->authorize('update', $group);

        $group->update($request->validated());

        return response()->json($group, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Group $group
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $group->delete();

        return response("Deleted", 204);
    }
}
