<?php

namespace App\Http\Controllers\Exports;

use App\Affiliate;
use App\Exports\AffiliateLeadsHideDepsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliates\ExportLeads;
use Maatwebsite\Excel\Facades\Excel;

class AffiliateLeadsHideDepsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Affiliate   $affiliate
     * @param ExportLeads $request
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Affiliate $affiliate, ExportLeads $request)
    {
        return Excel::download(new AffiliateLeadsHideDepsExport($affiliate, $request->validated()), 'affiliate-leads.xlsx');
    }
}
