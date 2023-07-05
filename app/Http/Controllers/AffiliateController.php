<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Http\Requests\Affiliates\Create;
use App\Http\Requests\Affiliates\Update;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Affiliate::class, 'affiliate');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Affiliate[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Affiliate::visible()
            ->with(['offer:id,name','trafficSource:id,name', 'branch:id,name'])
            ->searchIn('name', $request->input('search'))
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Affiliates\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $affiliate = Affiliate::create($request->validated());

        return response()->json($affiliate, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Affiliate $affiliate
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->loadMissing(['offer', 'trafficSource', 'branch']);

        return response()->json($affiliate->makeVisible('api_key'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Affiliates\Update $request
     * @param \App\Affiliate                       $affiliate
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Affiliate $affiliate)
    {
        $affiliate->update($request->validated());

        return response()->json($affiliate, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Affiliate $affiliate
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Affiliate $affiliate)
    {
        $affiliate->delete();

        return response(204);
    }
}
