<?php

namespace App\Http\Controllers\TrafficSources;

use App\Http\Controllers\Controller;
use App\TrafficSource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Domains extends Controller
{
    /**
     * Get domain assigned trafficSource.
     *
     * @param TrafficSource $trafficSource
     *
     * @return LengthAwarePaginator
     */
    public function __invoke(TrafficSource $trafficSource)
    {
        return $trafficSource->domains()->paginate();
    }
}
