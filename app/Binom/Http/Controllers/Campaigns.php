<?php

namespace App\Binom\Http\Controllers;

use App\Binom\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Campaigns extends Controller
{
    /**
     * Get campaigns list from the Binom
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        return Cache::remember('binom-campaigns', now()->addHour(), function () {
            return Campaign::whereHas('binom', fn ($query) => $query->active())->orderByDesc('campaign_id')->get();
        });
    }
}
