<?php

namespace App\Http\Controllers\BinomTrafficSources;

use App\Binom\TrafficSource as BinomTrafficSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\BinomTrafficSources\UpdateTrafficSource;

class TrafficSource extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param BinomTrafficSource                                         $binomTrafficSource
     * @param \App\Http\Requests\BinomTrafficSources\UpdateTrafficSource $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(BinomTrafficSource $binomTrafficSource, UpdateTrafficSource $request)
    {
        $this->authorize('update', $binomTrafficSource->binom);

        $binomTrafficSource->update($request->validated());

        return response()->json($binomTrafficSource->fresh(['innerTrafficSource']), 202);
    }
}
