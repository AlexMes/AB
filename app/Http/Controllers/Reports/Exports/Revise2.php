<?php

namespace App\Http\Controllers\Reports\Exports;

use App\Exports\Reports\Revise as ReviseExport;
use App\Http\Controllers\Controller;
use App\Reports\Revise2\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Revise2 extends Controller
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
        return Excel::download(new ReviseExport(Report::fromRequest($request)), 'revise-v2.xlsx');
    }
}
