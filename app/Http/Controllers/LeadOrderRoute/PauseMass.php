<?php

namespace App\Http\Controllers\LeadOrderRoute;

use App\Http\Controllers\Controller;
use App\LeadOrderRoute;
use Illuminate\Http\Request;

class PauseMass extends Controller
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
        $this->authorize('massPause', LeadOrderRoute::class);

        LeadOrderRoute::current()->visible()->update(['status' => LeadOrderRoute::STATUS_PAUSED]);

        return response(null, 204);
    }
}
