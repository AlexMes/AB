<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileLogs\Create;
use App\Http\Requests\ProfileLogs\Update;
use App\ProfileLog;
use Illuminate\Http\Request;

class ProfileLogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ProfileLog::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ProfileLog::query()
            ->with(['profile'])
            ->searchIn(['creative', 'link'], $request->input('search'))
            ->orderByDesc('id')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ProfileLogs\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(ProfileLog::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProfileLog $profileLog
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(ProfileLog $profileLog)
    {
        return response()->json($profileLog->loadMissing(['profile']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ProfileLogs\Update $request
     * @param \App\ProfileLog                       $profileLog
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, ProfileLog $profileLog)
    {
        return response()->json(tap($profileLog)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ProfileLog $profileLog
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileLog $profileLog)
    {
        $profileLog->delete();

        return response(null, 204);
    }
}
