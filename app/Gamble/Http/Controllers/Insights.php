<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\Http\Requests\Insights\Create;
use App\Gamble\Http\Requests\Insights\Update;
use App\Gamble\Insight;
use App\Http\Controllers\Controller;

class Insights extends Controller
{
    /**
     * Insights constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Insight::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return Insight::visible()
            ->with(['account', 'campaign', 'googleApp'])
            ->orderByDesc('created_at')
            ->paginate();
    }

    /**
     * @param Insight $insight
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Insight $insight)
    {
        return response()->json($insight->load(['account', 'campaign', 'googleApp']));
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        return response()->json(
            Insight::create($request->validated())->load(['account', 'campaign', 'googleApp']),
            201
        );
    }

    /**
     * @param Insight $insight
     * @param Update  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Insight $insight, Update $request)
    {
        $insight->update($request->validated());

        return response()->json($insight->fresh(['account', 'campaign', 'googleApp']), 202);
    }
}
