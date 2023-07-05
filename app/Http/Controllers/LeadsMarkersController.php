<?php

namespace App\Http\Controllers;

use App\Lead;
use App\LeadMarker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadsMarkersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Lead $lead)
    {
        $this->authorize('view', $lead);

        return $lead->markers()->paginate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead, LeadMarker $marker)
    {
        $this->authorize('view', $lead);

        DB::transaction(function () use ($lead, $marker) {
            $marker->delete();

            $lead->addEvent(Lead::MARKER_DELETED, [
                'id'     => $marker->id,
                'marker' => $marker->name,
            ]);
        });

        return response()->noContent();
    }
}
