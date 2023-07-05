<?php

namespace App\Http\Controllers\Imports\Leads;

use App\Http\Controllers\Controller;
use App\Imports\Leads\LeadsUpload;
use App\Lead;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LeadsUploadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('import', Lead::class);

        $filename = time() . '_' . Str::random(40) . '.' . $request->file('leads')->getClientOriginalExtension();
        Storage::disk('s3')->putFileAs('leads-uploads', $request->file('leads'), $filename);
        $leadsImport                 = new LeadsUpload();
        $leadsImport->offer_id       = $request->input('offer_id');
        $leadsImport->affiliate_id   = (int)$request->input('affiliate_id');
        $leadsImport->ignore_doubles = $request->input('ignore_doubles') == 'true';

        Excel::import($leadsImport, sprintf("leads-uploads/%s", basename($filename)), 's3');

        return response(201);
    }
}
