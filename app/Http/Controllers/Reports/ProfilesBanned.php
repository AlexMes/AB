<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ProfilesBanned\Report;
use Illuminate\Http\Request;

class ProfilesBanned extends Controller
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
