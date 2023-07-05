<?php

namespace App\Http\Controllers\StatusConfigs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusConfigs\Run;
use App\Jobs\SetAssignmentsStatuses;
use App\StatusConfig;
use Illuminate\Http\Request;

class RunController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Run          $request
     * @param StatusConfig $statusConfig
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Run $request, StatusConfig $statusConfig)
    {
        SetAssignmentsStatuses::dispatch($statusConfig);

        return response()->noContent();
    }
}
