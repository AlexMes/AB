<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Lead;
use Illuminate\Http\Request;

class AffiliateLeadsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, Affiliate $affiliate)
    {
        $this->authorize('viewAny', Lead::class);

        return response()->json($affiliate->leads()->with(['offer'])->orderByDesc('created_at')->paginate());
    }
}
