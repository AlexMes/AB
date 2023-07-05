<?php

namespace App\Http\Controllers\Enums;

use App\Http\Controllers\Controller;
use App\LeadMarker;
use Str;

class LeadsMarkers extends Controller
{
    /**
     * Get all unique markers for leads
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return cache()->remember(
            'leads-markers',
            now()->addMinutes(10),
            fn () => LeadMarker::query()->distinct()->pluck('name')->map(fn ($marker) => ['id' => $marker,'name' => Str::ucfirst($marker)])
        );
    }
}
