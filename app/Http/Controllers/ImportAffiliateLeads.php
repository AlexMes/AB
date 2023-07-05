<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;

class ImportAffiliateLeads extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Affiliate           $affiliate
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function __invoke(Affiliate $affiliate, Request $request)
    {
        $this->authorize('import', Lead::class);

        $file     = $request->file('leads');
        $filename = sprintf("aff_%s_leads_%s.%s", $affiliate->id, time(), $file->getClientOriginalExtension());

        Storage::disk('s3')->putFileAs('leads', $file, $filename);

        return response()->json([
            'filename' => $filename,
            'fields'   => (new HeadingRowImport())->toCollection($file)->flatten(2)->toArray()
        ], 201);
    }
}
