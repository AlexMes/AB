<?php

namespace App\Http\Controllers\Adsets;

use App\Facebook\AdSet;
use App\Http\Controllers\Controller;

class StartMidnight extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Adset $adset
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdSet $adset)
    {
        $this->authorize('update', $adset);

        return response()->json(tap($adset)->update(['start_midnight' => true]), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Facebook\Adset $adset
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AdSet $adset)
    {
        $this->authorize('update', $adset);

        return response()->json(tap($adset)->update(['start_midnight' => false]), 202);
    }
}
