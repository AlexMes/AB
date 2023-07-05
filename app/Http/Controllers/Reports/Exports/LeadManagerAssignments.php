<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\LeadManagerAssignments as Export;
use App\Http\Controllers\Controller;
use App\Reports\LeadManagerAssignments\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadManagerAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is not authorized.');
        }

        return Excel::download(new Export(Report::fromRequest($request)), 'lead-manager-assignments.xlsx');
    }
}
