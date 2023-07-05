<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;

class PlacementListController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function __invoke(Request $request)
    {
        return cache()->remember('utm-medium', now()->addHour(), function () {
            return Lead::select('utm_medium')
                ->distinct()
                ->orderBy('utm_medium')
                ->pluck('utm_medium')
                ->reject(fn ($placement) => empty($placement))
                ->values();
        });
    }
}
