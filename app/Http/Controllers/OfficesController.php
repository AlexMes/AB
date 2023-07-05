<?php

namespace App\Http\Controllers;

use App\Http\Requests\Offices\CreateOfficeRequest;
use App\Http\Requests\Offices\UpdateOfficeRequest;
use App\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class OfficesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \App\Office[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed[]
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Office::class);

        return Office::visible()
            ->searchIn('name', $request->search)
            ->orderBy('id')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateOfficeRequest $request
     *
     * @return Office|\Illuminate\Database\Eloquent\Model|Response
     */
    public function store(CreateOfficeRequest $request)
    {
        $office = Office::create(Arr::except($request->validated(), ['morning_branches']));

        $office->morningBranches()->sync($request->input('morning_branches', []));

        return $office;
    }

    /**
     * Display the specified resource.
     *
     * @param Office $office
     *
     * @return Office
     */
    public function show(Office $office)
    {
        $this->authorize('view', $office->loadMissing(['destination', 'morningBranches']));

        return $office;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOfficeRequest $request
     * @param Office              $office
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function update(UpdateOfficeRequest $request, Office $office)
    {
        $office->update(Arr::except($request->validated(), ['morning_branches']));

        $office->morningBranches()->sync($request->input('morning_branches', []));

        return response()->json($office, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Office $office
     *
     * @return Response
     */
    public function destroy(Office $office)
    {
        $this->authorize('destroy', $office);

        return response('Forbidden', 403);
    }
}
