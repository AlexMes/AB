<?php

namespace App\Http\Controllers\Reports;

use App\Reports\Revenue\Report;
use Illuminate\Http\Request;

class Revenue extends \App\Http\Controllers\Controller
{
    /**
     * Build revenue report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return Report::fromRequest($request);
    }
}
