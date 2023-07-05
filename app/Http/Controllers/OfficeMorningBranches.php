<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Requests\CreateOfficeMorningBranch;
use App\Http\Requests\DeleteOfficeMorningBranch;
use App\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OfficeMorningBranches extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Office  $office
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Office $office, Request $request)
    {
        $this->authorize('view', $office);

        return response()->json($office->morningBranches);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Office                    $office
     * @param CreateOfficeMorningBranch $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Office $office, CreateOfficeMorningBranch $request)
    {
        if ($office->morningBranches()->where('branch_id', $request->input('branch_id'))->doesntExist()) {
            $office->morningBranches()->attach(
                $request->input('branch_id'),
                Arr::except($request->validated(), ['branch_id'])
            );
        }

        return response()->json(
            $office->morningBranches()->where('branch_id', $request->input('branch_id'))->first(),
            201
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Office               $office
     * @param Branch                    $morningBranch
     * @param DeleteOfficeMorningBranch $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Office $office, Branch $morningBranch, DeleteOfficeMorningBranch $request)
    {
        $office->morningBranches()->detach($morningBranch->id);

        return response()->noContent();
    }
}
