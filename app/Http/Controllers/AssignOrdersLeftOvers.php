<?php

namespace App\Http\Controllers;

use App\Jobs\RunLeftOversAssignment;
use App\Lead;
use Cache;
use Illuminate\Http\Request;

class AssignOrdersLeftOvers extends Controller
{
    /**
     * Push assigner jobs for leads
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('assignLeftovers', Lead::class);

        $lock = Cache::lock('leftovers-split', 15);

        $request->validate([
            'amount' => array_merge(
                ['required','int','min:1'],
                $request->boolean('simulate_autologin') ? ['max:1'] : [],
            ),
        ], ['amount.max' => 'The :attribute may not be greater than :max when simulate_autologin is on.']);

        if ($lock->get()) {
            RunLeftOversAssignment::dispatchNow(
                $request->input('date'),
                $request->input('amount'),
                $request->input('offers'),
                $request->input('offices'),
                $request->input('leads'),
                $request->boolean('simulate_autologin'),
                null,
                $request->boolean('retry') && auth()->user()->branch_id === 19,
            );

            return response()->noContent();
        }

        return response()->json(['message' => 'Splitter already running'], 400);
    }
}
