<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\Http\Requests\TechCosts\Create;
use App\Gamble\Http\Requests\TechCosts\Update;
use App\Gamble\TechCost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechCostController extends Controller
{
    /**
     * TechCostController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(TechCost::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TechCost::visible()
            ->with(['user'])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate();
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
        return response()->json(TechCost::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Gamble\TechCost $techCost
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(TechCost $techCost)
    {
        return response()->json($techCost->load(['user']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update               $request
     * @param \App\Gamble\TechCost $techCost
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, TechCost $techCost)
    {
        return response()->json(tap($techCost)->update($request->validated()), 202);
    }
}
