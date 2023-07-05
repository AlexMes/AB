<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Requests\Branches\Create;
use App\Http\Requests\Branches\Update;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Branch constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Branch::class);
    }

    /**
     * * Display a listing of the resource.
     *
     * @return \App\Branch[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        return Branch::query()
            ->searchIn(['name'], $request->input('search'))
            ->when(
                $request->has('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get(['id','name'])
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
        ($branch = new Branch())->mergeCasts(['sms_config' => null]);
        $branch->fill($request->validated())->save();

        return response()->json($branch, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Branch $branch
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return response()->json($branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update      $request
     * @param \App\Branch $branch
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Branch $branch)
    {
        $branch->mergeCasts(['sms_config' => null]);
        $branch->update($request->validated());

        return response()->json($branch, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Branch $branch
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->noContent();
    }
}
