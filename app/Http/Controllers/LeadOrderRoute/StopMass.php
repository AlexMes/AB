<?php

namespace App\Http\Controllers\LeadOrderRoute;

use App\Http\Controllers\Controller;
use App\LeadOrderRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StopMass extends Controller
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
        $this->authorize('massStop', LeadOrderRoute::class);

        LeadOrderRoute::current()->visible()->update(['leadsOrdered' => DB::raw('"leadsReceived"')]);

        return response(null, 204);
    }
}
