<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Offer;
use App\ResellBatch;
use Illuminate\Http\Request;

class ProgressStatsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', ResellBatch::class);

        return Offer::allowed()
            ->select(['id', 'name'])
            /*->current()*/
            ->withResellOrderedCount()
            ->withResellReceivedCount()
            ->get()
            ->reject(fn (Offer $offer) => $offer->resell_ordered === 0 && $offer->resell_received === 0)
            ->values()
            ->toArray();
    }
}
