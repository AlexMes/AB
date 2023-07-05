<?php

namespace App\Http\Controllers;

use App\DistributionRule;
use App\Http\Requests\DistributionRules\Create;
use App\Http\Requests\DistributionRules\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DistributionRulesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DistributionRule::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            DistributionRule::visible()
                ->with(['offer', 'office'])
                ->when($request->input('office_id'), function ($query, $input) {
                    $input = Arr::wrap($input);

                    return $query->where(function ($q) use ($input) {
                        if (($index = array_search('null', $input)) !== false) {
                            $q->whereNull('office_id');

                            Arr::forget($input, $index);
                        }

                        return $q->orWhereIn('office_id', $input);
                    });
                })
                ->when(
                    $request->input('offer_id'),
                    fn ($query, $input) => $query->whereIn('offer_id', Arr::wrap($input))
                )
                ->when($request->input('geo'), fn ($query, $input) => $query->whereIn('geo', Arr::wrap($input)))
                ->searchIn(['country_name'], $request->input('search'))
                ->orderBy('country_name')
                ->orderByRaw('office_id asc nulls first')
                ->orderBy('offer_id', 'desc')
                ->when(
                    $request->has('paginate'),
                    fn ($query) => $query->paginate(),
                    fn ($query) => $query->get()
                )
        );
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
        return response()->json(
            DistributionRule::firstOrCreate(
                $request->only(['office_id', 'offer_id', 'geo']),
                $request->only(['allowed', 'country_name'])
            )->refresh()->loadMissing(['offer']),
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DistributionRule $distributionRule
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(DistributionRule $distributionRule)
    {
        return response()->json($distributionRule->loadMissing(['offer', 'office']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update                $request
     * @param \App\DistributionRule $distributionRule
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, DistributionRule $distributionRule)
    {
        return response()->json(
            tap($distributionRule)->update($request->validated())->loadMissing(['offer', 'office']),
            202
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DistributionRule $distributionRule
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DistributionRule $distributionRule)
    {
        $distributionRule->delete();

        return response()->noContent();
    }
}
