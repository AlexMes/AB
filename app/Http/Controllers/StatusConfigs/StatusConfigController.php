<?php

namespace App\Http\Controllers\StatusConfigs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusConfigs\Create;
use App\Http\Requests\StatusConfigs\Update;
use App\StatusConfig;

class StatusConfigController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StatusConfig::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(StatusConfig::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\StatusConfig $statusConfig
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(StatusConfig $statusConfig)
    {
        return response()->json($statusConfig);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update            $request
     * @param \App\StatusConfig $statusConfig
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, StatusConfig $statusConfig)
    {
        return response()->json(tap($statusConfig)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\StatusConfig $statusConfig
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusConfig $statusConfig)
    {
        $statusConfig->delete();

        return response()->noContent();
    }
}
