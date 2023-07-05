<?php

namespace App\CRM\Http\Controllers\Exports;

use App\CRM\Exports\LeadsExport;
use App\CRM\LeadOrderAssignment;
use App\CRM\Queries\ManagerAssignments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Assignments extends Controller
{
    /**
     * @var \App\CRM\Queries\ManagerAssignments
     */
    protected ManagerAssignments $assignments;

    /**
     * Assignments constructor.
     *
     * @param \App\CRM\Queries\ManagerAssignments $assignments
     */
    public function __construct(ManagerAssignments $assignments)
    {
        $this->assignments = $assignments;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        $this->authorize('export', LeadOrderAssignment::class);

        $assignments = $this->assignments
            ->search($request->get('search'))
            ->forOffice($request->get('office'))
            ->forManager($request->get('manager'))
            ->forOffer($request->get('offer'))
            ->havingStatus($request->get('status'))
            ->forPeriod($request->get('period'))
            ->forLabels($request->get('label'))
            ->forGender($request->get('gender'))
            ->forBranch($request->get('branch'))
            ->forOfficeGroup($request->get('office_group'))
            ->forAffiliate($request->get('affiliate'))
            ->forSmoothLo($request->get('smooth_lo', 'without_delayed'))
            ->get();

        return Excel::download(new LeadsExport($assignments), 'assignments.xlsx');
    }
}
