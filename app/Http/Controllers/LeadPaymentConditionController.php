<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadPaymentConditions\Create;
use App\Http\Requests\LeadPaymentConditions\Update;
use App\LeadPaymentCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LeadPaymentConditionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LeadPaymentCondition::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LeadPaymentCondition::query()
            ->with(['office', 'offer'])
            ->searchIn(['model', 'cost'], $request->input('search'))
            ->when($request->input('office_id'), fn ($q, $input) => $q->whereIn('office_id', Arr::wrap($input)))
            ->when($request->input('offer_id'), fn ($q, $input) => $q->whereIn('offer_id', Arr::wrap($input)))
            ->when(
                $request->input('sort') === 'cost',
                fn ($query) => $query->orderByRaw('cost::decimal ' . ($request->boolean('asc') ? 'asc' : 'desc')),
                fn ($query) => $query->orderBy($request->input('sort', 'id'), $request->boolean('asc') ? 'asc' : 'desc')
            )
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(LeadPaymentCondition::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(LeadPaymentCondition $leadPaymentCondition)
    {
        return response()->json($leadPaymentCondition->loadMissing(['office', 'offer']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, LeadPaymentCondition $leadPaymentCondition)
    {
        return response()->json(
            tap($leadPaymentCondition)->update($request->validated())->loadMissing(['office', 'offer']),
            202
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LeadPaymentCondition $leadPaymentCondition
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeadPaymentCondition $leadPaymentCondition)
    {
        $leadPaymentCondition->delete();

        return response()->noContent();
    }
}
