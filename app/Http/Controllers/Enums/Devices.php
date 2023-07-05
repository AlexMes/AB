<?php

namespace App\Http\Controllers\Enums;

use App\Binom\Click;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Devices extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return Cache::remember('devices', now()->addHours(4), fn () => Click::distinct()->pluck('device_pointing_method'));
    }
}
