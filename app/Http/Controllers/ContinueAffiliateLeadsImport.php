<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Imports\AffiliateLeadsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ContinueAffiliateLeadsImport extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Affiliate           $affiliate
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $filename
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Affiliate $affiliate, $filename, Request $request)
    {
        Excel::import(
            new AffiliateLeadsImport($affiliate, $request->all()),
            sprintf("leads/%s", $filename),
            's3',
            Str::ucfirst(File::extension(sprintf("%s/app/leads/%s", storage_path(), $filename)))
        );

        return response()->json([
            'message'  => 'Import stared',
            'filename' => $filename
        ]);
    }
}
