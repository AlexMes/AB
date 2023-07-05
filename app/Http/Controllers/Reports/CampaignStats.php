<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\CampaignStats\Report;
use Illuminate\Http\Request;

class CampaignStats extends Controller
{
    /**
     * Load quick stats for dashboard
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\CampaignStats\Report
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
