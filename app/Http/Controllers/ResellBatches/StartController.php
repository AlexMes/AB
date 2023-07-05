<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResellBatches\Start;
use App\Jobs\StartResellBatch;
use App\ResellBatch;

class StartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ResellBatches\Start $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(ResellBatch $resellBatch, Start $request)
    {
        $resellBatch->update(array_merge($request->validated(), ['status' => ResellBatch::IN_PROCESS]));

        StartResellBatch::dispatchNow($resellBatch);

        $resellBatch->load(['leads'])->loadMissing(['offices'])
            ->leads->each(function ($lead) use ($resellBatch) {
                $lead->offer_id = $resellBatch->substitute_offer;
            });

        return response()->json($resellBatch, 202);
    }
}
