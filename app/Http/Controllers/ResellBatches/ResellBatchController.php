<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResellBatches\Create;
use App\Http\Requests\ResellBatches\Update;
use App\ResellBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ResellBatchController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ResellBatch::class);
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
        return ResellBatch::visible()
            ->with(['substituteOffer', 'offices'])
            ->searchIn(['name'], $request->input('search'))
            ->orderByDesc('created_at')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ResellBatches\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $resellBatch = ResellBatch::create(Arr::except($request->validated(), ['offices']));

        $resellBatch->offices()->sync($request->input('offices'));

        return response()->json($resellBatch, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ResellBatch $resellBatch
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(ResellBatch $resellBatch)
    {
        return response()->json($resellBatch->loadMissing(['leads', 'offices','leads.ipAddress','leads.assignments.route','substituteOffer']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ResellBatches\Update $request
     * @param \App\ResellBatch                        $resellBatch
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, ResellBatch $resellBatch)
    {
        $resellBatch->update(Arr::except($request->validated(), ['offices']));

        $resellBatch->offices()->sync($request->input('offices'));

        return response()->json($resellBatch, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ResellBatch $resellBatch
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResellBatch $resellBatch)
    {
        //
    }
}
