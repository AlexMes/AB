<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\IpAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountriesController extends Controller
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
            'geo-countries',
            now()->addHours(4),
            fn () => IpAddress::distinct('country_name')->get(['country', 'country_name'])
        );
    }
}
