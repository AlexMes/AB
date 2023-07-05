<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\AdDisapprovalReason\Report;
use Illuminate\Http\Request;

class AdDisapprovalReason extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
