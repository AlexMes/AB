<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResellBatches\Cancel;
use App\ResellBatch;

class CancelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ResellBatches\Cancel $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(ResellBatch $resellBatch, Cancel $request)
    {
        $resellBatch->update(array_merge($request->validated(), ['status' => ResellBatch::CANCELED]));

        $resellBatch->loadMissing(['leads','offices'])
            ->leads->each(function ($lead) use ($resellBatch) {
                $lead->offer_id = $resellBatch->substitute_offer;
            });

        return response()->json($resellBatch, 202);
    }
}
