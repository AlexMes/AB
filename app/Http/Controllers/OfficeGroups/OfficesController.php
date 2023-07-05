<?php

namespace App\Http\Controllers\OfficeGroups;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeGroups\AttachOffice;
use App\Office;
use App\OfficeGroup;
use Illuminate\Http\Request;

class OfficesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param OfficeGroup $officeGroup
     * @param Request     $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(OfficeGroup $officeGroup, Request $request)
    {
        $this->authorize('view', $officeGroup);

        return $officeGroup->offices()
            ->orderByDesc('office_office_group.id')
            ->paginate(50);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OfficeGroup  $officeGroup
     * @param AttachOffice $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(OfficeGroup $officeGroup, AttachOffice $request)
    {
        $officeGroup->offices()->syncWithoutDetaching($request->input('office_id'));

        return response()->json($officeGroup->offices()->where('offices.id', $request->office_id)->first(), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OfficeGroup $officeGroup
     * @param Office           $office
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficeGroup $officeGroup, Office $office)
    {
        $this->authorize('update', $officeGroup);

        $officeGroup->offices()->detach($office);

        return response()->noContent();
    }
}
