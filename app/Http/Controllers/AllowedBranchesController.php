<?php

namespace App\Http\Controllers;

use App\AllowedBranch;
use App\Http\Requests\AllowedBranches\Create;
use App\Http\Requests\AllowedBranches\Delete;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AllowedBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            AllowedBranch::query()
                ->with(['user', 'branch'])
                ->when($request->input('user_id'), fn ($query, $input) => $query->whereIn('user_id', Arr::wrap($input)))
                ->when(
                    $request->input('branch_id'),
                    fn ($query, $input) => $query->whereIn('branch_id', Arr::wrap($input))
                )
                ->orderBy('id')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(AllowedBranch::firstOrCreate($request->validated())->load(['user', 'branch']), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Delete        $request
     * @param AllowedBranch $allowedBranch
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Delete $request, AllowedBranch $allowedBranch)
    {
        $allowedBranch->delete();

        return response()->noContent();
    }
}
