<?php

namespace App\Http\Controllers\ResellBatches;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResellBatches\CreateLead;
use App\ResellBatch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ResellBatches\CreateLead $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(ResellBatch $resellBatch, CreateLead $request)
    {
        DB::transaction(function () use ($resellBatch, $request) {
            $resellBatch->update([
                'filters' => Arr::except($request->input(), ['leads']),
            ]);

            $resellBatch->leads()->syncWithoutDetaching($request->input('leads'));
        });

        return response()->json($resellBatch->fresh(['leads', 'offices', 'leads.ipAddress', 'leads.assignments']), 201);
    }
}
