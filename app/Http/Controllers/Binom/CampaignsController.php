<?php

namespace App\Http\Controllers\Binom;

use App\Binom\Campaign;
use App\Http\Controllers\Controller;
use App\Http\Requests\Binoms\CampaignUpdate;
use Illuminate\Http\Request;

class CampaignsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Campaign::class, 'campaign');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Campaign[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Campaign::query()
            ->searchIn('name', $request->search)
            ->notEmptyWhere('binom_id', $request->input('binom_id'))
            ->with(['binom', 'offer'])
            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param Campaign $campaign
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        return response()->json($campaign, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CampaignUpdate $request
     * @param Campaign       $campaign
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(CampaignUpdate $request, Campaign $campaign)
    {
        $campaign->update($request->validated());

        return response()->json($campaign->load(['offer', 'binom']), 202);
    }
}
