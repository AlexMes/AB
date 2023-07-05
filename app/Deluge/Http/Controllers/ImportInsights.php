<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Imports\InsightsImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportInsights extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        foreach ($request->file('files') as $file) {
            $filename = time() . '_' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->putFileAs('statistics', $file, $filename);
            Excel::import(new InsightsImport(), sprintf("statistics/%s", basename($filename)), 's3');
        }

        return back()->with('message', 'Импорт начат.');
    }
}
