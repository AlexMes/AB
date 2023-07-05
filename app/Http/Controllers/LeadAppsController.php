<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeadAppsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Cache::remember(
            sprintf('lead-apps-for-%s', auth()->id()),
            now()->addMinutes(30),
            fn () => Lead::visible()->whereNotNull('app_id')->distinct('app_id')->get(['app_id'])->pluck('app_id')
        );
    }
}
