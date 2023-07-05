<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SmsStats\Report;
use Illuminate\Http\Request;

class SmsStats extends Controller
{
    /**
     * Load sms stats for sms messages
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
