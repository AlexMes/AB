<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Requests\Sms\CreateSmsCampaignRequest;
use App\Http\Requests\Sms\UpdateSmsCampaignRequest;
use App\SmsCampaign;
use Illuminate\Http\Request;

class DomainSmsCampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Domain  $domain
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Domain $domain, Request $request)
    {
        $this->authorize('view', SmsCampaign::class);

        return response()->json($domain->smsCampaigns()->with(['branch'])->orderByDesc('id')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Sms\CreateSmsCampaignRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Domain $domain, CreateSmsCampaignRequest $request)
    {
        return response()->json(
            $domain->smsCampaigns()
                ->create($request->validated())
                ->load(['landing', 'branch'])
                ->loadCount(['messages']),
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Sms\UpdateSmsCampaignRequest $request
     * @param \App\Domain                                     $domain
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Domain $domain, SmsCampaign $smsCampaign, UpdateSmsCampaignRequest $request)
    {
        return response()->json(tap($smsCampaign)->update($request->validated())->load(['branch']), 202);
    }
}
