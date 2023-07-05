<?php

namespace App\Http\Controllers\Imports\Leads;

use App\AdsBoard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\Import as ImportRequest;
use App\Imports\Leads\Import as LeadsImport;
use App\Jobs\Imports\ClearUpload;
use App\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\HeadingRowImport;

class Import extends Controller
{
    /**
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('import', Lead::class);

        $file     = $request->file('leads');
        $filename = 'leads_' . time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs('leads', $file, $filename);

        return response([
            'filename' => $filename,
            'fields'   => (new HeadingRowImport())->toCollection($file)->flatten(2)->toArray()
        ], 201);
    }

    /**
     * @param $filename
     * @param ImportRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update($filename, ImportRequest $request)
    {
        (new LeadsImport($request->validated()))
            ->queue(
                "leads/{$filename}",
                'public',
                Str::ucfirst(File::extension(storage_path() . "app/leads/{$filename}"))
            )
            ->allOnQueue(AdsBoard::QUEUE_IMPORTS);

        return response($filename, 202);
    }

    /**
     * @param $filename
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($filename)
    {
        $this->authorize('import', Lead::class);

        ClearUpload::dispatch("leads/{$filename}")->onQueue(AdsBoard::QUEUE_IMPORTS);

        return response("Deleted", 204);
    }
}
