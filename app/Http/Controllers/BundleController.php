<?php

namespace App\Http\Controllers;

use App\Bundle;
use App\Http\Requests\Bundles\Create;
use App\Http\Requests\Bundles\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Bundle::class);

        return Bundle::query()
            ->with(['offer', 'placements'])
            ->searchIn(['prelend_link', 'lend_link', 'title'], $request->get('search'))
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return void
     */
    public function store(Create $request)
    {
        $bundle = Bundle::create(Arr::except($request->validated(), ['placements']));

        $placementIds = $request->get('placements', []);
        $bundle->placements()->attach($placementIds);

        return response()->json($bundle, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Bundle $bundle
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Bundle $bundle)
    {
        $this->authorize('view', $bundle);

        return response()->json($bundle->load(['offer', 'placements']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update      $request
     * @param \App\Bundle $bundle
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Bundle $bundle)
    {
        $bundle->update(Arr::except($request->validated(), ['placements']));

        $placementIds = $request->get('placements', []);
        $bundle->placements()->sync($placementIds);

        return response()->json($bundle, 202);
    }
}
