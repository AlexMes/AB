<?php

namespace App\Http\Controllers\Exports;

use App\Deposit;
use App\Exports\DepositsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DepositsExportController extends Controller
{

    /**
     * DepositsController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Deposit::class, 'deposit');
    }

    /**
     * List visible deposits
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        return Excel::download(new DepositsExport($request), 'deposits.csv');
    }
}
