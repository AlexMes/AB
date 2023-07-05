<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTrafficSource;
use App\Http\Requests\Suppliers\Update;
use App\Http\Requests\UpdateTrafficSource;
use App\TrafficSource;
use Illuminate\Http\Request;

class TrafficSourcesController extends Controller
{
    /**
     * SuppliersController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(TrafficSource::class, 'traffic');
    }

    /**
     * Load all access suppliers
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\TrafficSource[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        return TrafficSource::query()
            ->searchIn(['name'], $request->get('search'))
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateTrafficSource $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateTrafficSource $request)
    {
        return response()->json(TrafficSource::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TrafficSource $trafficSource
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(TrafficSource $trafficSource)
    {
        return response()->json($trafficSource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTrafficSource $request
     * @param \App\TrafficSource                     $trafficSource
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(UpdateTrafficSource $request, TrafficSource $trafficSource)
    {
        return response()->json(tap($trafficSource)->update($request->validated()), 202);
    }
}
