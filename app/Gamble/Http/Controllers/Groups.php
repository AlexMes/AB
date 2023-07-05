<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\Group;
use App\Gamble\Http\Requests\Groups\Create;
use App\Gamble\Http\Requests\Groups\Update;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Groups extends Controller
{
    /**
     * Groups constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Group::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Group::query()
            ->orderBy('name')
            ->when(
                $request->has('all'),
                fn ($query) => $query->get(),
                fn ($query) => $query->paginate()
            );
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Group $group)
    {
        return response()->json($group);
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        return response()->json(Group::create($request->validated()), 201);
    }

    /**
     * @param Group  $group
     * @param Update $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Group $group, Update $request)
    {
        $group->update($request->validated());

        return response()->json($group->fresh(), 202);
    }
}
