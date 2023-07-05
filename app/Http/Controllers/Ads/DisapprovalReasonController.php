<?php

namespace App\Http\Controllers\Ads;

use App\Facebook\AdDisapproval;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisapprovalReasonController extends Controller
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
        return AdDisapproval::select('reason')
            ->distinct()
            ->orderBy('reason')
            ->pluck('reason');
    }
}
