<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadsDestinations\Create;
use App\Http\Requests\LeadsDestinations\Update;
use App\Http\Requests\LeadsDestinations\View;
use App\LeadDestination;
use Illuminate\Http\Request;

class LeadDestinationController extends Controller
{
    /**
     * Load destinations
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return \App\Http\Resources\LeadDestination::collection(LeadDestination::visible()
            ->when($request->filled('office_id'), function ($query) use ($request) {
                return $query->where(
                    fn ($q) => $q->where('lead_destinations.office_id', $request->input('office_id'))
                        ->orWhereNull('lead_destinations.office_id')
                );
            })
            ->when($request->input('search'), fn ($query, $input) => $query->searchIn(['name', 'driver'], $input))
            ->when($request->boolean('active'), fn ($query) => $query->whereIsActive(true))
            ->orderByDesc('id')
            ->when(
                $request->has('paginate'),
                fn ($query) => $query->with(['branch', 'office'])->paginate(),
                fn ($query) => $query->get(['id','name'])->prepend(LeadDestination::default())
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        ($leadDestination = new LeadDestination())->mergeCasts(['configuration' => null]);
        $leadDestination->fill($request->validated())->save();

        return response()->json($leadDestination, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\LeadDestination $leadsDestination
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(View $request, LeadDestination $leadsDestination)
    {
        return response()->json($leadsDestination->makeVisible('configuration')->loadMissing(['branch', 'office']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update               $request
     * @param \App\LeadDestination $leadsDestination
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, LeadDestination $leadsDestination)
    {
        $leadsDestination->mergeCasts(['configuration' => null]);
        $leadsDestination->update($request->validated());

        return response()->json($leadsDestination, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LeadDestination $leadsDestination
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeadDestination $leadsDestination)
    {
        return response()->noContent();
    }
}
