<?php

namespace App\Http\Controllers\OfficeGroups;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeGroups\Create;
use App\Http\Requests\OfficeGroups\Update;
use App\OfficeGroup;
use Illuminate\Http\Request;

class OfficeGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OfficeGroup::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return OfficeGroup::visible()
            ->with(['branch'])
            ->searchIn(['name'], $request->input('search'))
            ->orderByDesc('created_at')
            ->when(
                $request->filled('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get(['id', 'name']),
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
        return response()->json(OfficeGroup::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\OfficeGroup $officeGroup
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(OfficeGroup $officeGroup)
    {
        return response()->json($officeGroup->load(['branch']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update           $request
     * @param \App\OfficeGroup $officeGroup
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, OfficeGroup $officeGroup)
    {
        return response()->json(tap($officeGroup)->update($request->validated())->load(['branch']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OfficeGroup $officeGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficeGroup $officeGroup)
    {
        $officeGroup->delete();

        return response()->noContent();
    }
}
