<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\AccountsBanned\Report;
use Illuminate\Http\Request;

class AccountsBanned extends Controller
{
    /**
     * @param Request $request
     *
     * @return Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
