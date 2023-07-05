<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sms\CreateSmsCampaignRequest;
use App\Http\Requests\Sms\UpdateSmsCampaignRequest;
use App\SmsCampaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * CampaignController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(SmsCampaign::class, 'campaign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return SmsCampaign::query()
            ->searchIn(['title'], $request->title)
            ->orderByDesc('id')
            ->unless($request->has('all'), function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateSmsCampaignRequest $request
     *
     * @return \App\SmsCampaign|\Illuminate\Database\Eloquent\Model
     */
    public function store(CreateSmsCampaignRequest $request)
    {
        return SmsCampaign::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param SmsCampaign $campaign
     *
     * @return SmsCampaign
     */
    public function show(SmsCampaign $campaign)
    {
        return $campaign;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSmsCampaignRequest $request
     * @param SmsCampaign              $campaign
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSmsCampaignRequest $request, SmsCampaign $campaign)
    {
        return response()->json(tap($campaign)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SmsCampaign $campaign
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SmsCampaign $campaign)
    {
        return response('Forbidden', 403);
    }
}
