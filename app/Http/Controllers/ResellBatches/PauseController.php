<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResellBatches\Pause;
use App\ResellBatch;

class PauseController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ResellBatches\Pause $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(ResellBatch $resellBatch, Pause $request)
    {
        $resellBatch->update(array_merge($request->validated(), ['status' => ResellBatch::PAUSED]));

        $resellBatch->loadMissing(['leads','offices'])
            ->leads->each(function ($lead) use ($resellBatch) {
                $lead->offer_id = $resellBatch->substitute_offer;
            });

        return response()->json($resellBatch, 202);
    }
}
