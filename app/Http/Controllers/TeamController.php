<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teams\Create;
use App\Http\Requests\Teams\Update;
use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Team::class);
    }

    /**
     * Index all teams
     *
     * @return Team[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        return Team::visible()
            ->searchIn(['name'], $request->input('search'))
            ->when($request->has('branch'), fn ($query) => $query->where('branch_id', $request->input('branch')))
            ->orderBy('id')
            ->when(
                $request->has('all'),
                fn ($q) => $q->get(),
                fn ($q) => $q->paginate()
            );
    }

    /**
     * Store team
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        return response()->json(Team::create($request->validated()), 201);
    }

    /**
     * Load single team
     *
     * @param Team $team
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Team $team)
    {
        return response()->json($team->load('users'));
    }

    /**
     * Update team
     *
     * @param Update $request
     * @param Team   $team
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request, Team $team)
    {
        return response()->json(tap($team)->update($request->validated()), 202);
    }

    /**
     * Delete team
     *
     * @param Team $team
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->noContent();
    }
}
