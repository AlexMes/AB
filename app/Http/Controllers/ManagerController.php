<?php

namespace App\Http\Controllers;

use App\Http\Requests\Managers\Update;
use App\Manager;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * ManagerController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Manager::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Manager[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Http\Response|\Illuminate\Support\Collection
     */
    public function index()
    {
        return Manager::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Manager $manager
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Manager $manager)
    {
        return response()->json($manager->loadMissing(['office']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Managers\Update $request
     * @param \App\Manager                       $manager
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Manager $manager)
    {
        return response()->json(tap($manager)->update($request->validated())->load(['office']), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Manager $manager
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manager $manager)
    {
        //
    }
}
