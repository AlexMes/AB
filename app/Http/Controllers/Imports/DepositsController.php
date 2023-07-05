<?php

namespace App\Http\Controllers\Imports;

use App\AdsBoard;
use App\Deposit;
use App\Http\Controllers\Controller;
use App\Imports\DepositsImport;
use App\Jobs\Imports\ClearUpload;
use App\Jobs\Imports\ImportDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class DepositsController extends Controller
{
    public function store(Request $request)
    {
        /*$this->authorize('create', Deposit::class);

        $filename = "deposits_" . time();

        Storage::disk('tmp')
            ->putFileAs('/deposits/', $request->file('deposits'), $filename);

        return response([
            'file'    => $filename,
            'headers' => (new HeadingRowImport())
                ->toCollection("deposits/{$filename}", 'tmp', Excel::XLSX)
                ->first()
                ->flatten()
                ->toArray(),
            'data' => (new DepositsImport(auth()->user()))
                ->toCollection("deposits/{$filename}", 'tmp', Excel::XLSX)
                ->first()
        ], 201);*/

        $this->authorize('create', Deposit::class);

        throw ValidationException::withMessages([
            'message' => "Dont use this feature now, please."
        ]);

        ImportDeposit::dispatch($request->deposits)->onQueue(AdsBoard::QUEUE_IMPORTS);

        return 201;
    }

    public function destroy($filename)
    {
        ClearUpload::dispatch("deposits/{$filename}")->onQueue('imports');

        return response("Deleted", 204);
    }
}
