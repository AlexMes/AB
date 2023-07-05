<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfficeStatuses\Create;
use App\Http\Requests\OfficeStatuses\Update;
use App\OfficeStatus;
use Illuminate\Http\Request;

class OfficeStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OfficeStatus::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            OfficeStatus::query()
                ->when($request->input('office_id'), fn ($query, $input) => $query->whereOfficeId($input))
                ->orderBy('status')
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
        return response()->json(
            OfficeStatus::firstOrCreate(
                $request->only(['office_id', 'status']),
                $request->only(['selectable'])
            )->refresh(),
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update            $request
     * @param \App\OfficeStatus $officeStatus
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, OfficeStatus $officeStatus)
    {
        return response()->json(tap($officeStatus)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OfficeStatus $officeStatus
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficeStatus $officeStatus)
    {
        $officeStatus->delete();

        return response()->noContent();
    }
}
