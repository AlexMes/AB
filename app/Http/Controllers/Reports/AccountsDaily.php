<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\AccountsDaily\Report;
use Illuminate\Http\Request;

class AccountsDaily extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\AccountsDaily\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
