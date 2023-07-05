<?php

namespace App\Deluge\Http\Controllers\Campaigns;

use App\Deluge\Http\Requests\Insights\Create;
use App\Http\Controllers\Controller;
use App\ManualCampaign;
use App\ManualInsight;
use Illuminate\Http\Request;

class Insights extends Controller
{
    /**
     * Index insights
     *
     * @param ManualCampaign           $campaign
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View|void
     */
    public function index(ManualCampaign $campaign, Request $request)
    {
        $this->authorize('view', $campaign);

        return view('deluge::campaigns.show', [
            'campaign' => $campaign,
            'insights' => ManualInsight::allowedOffers()
                ->visible()
                ->with(['account', 'campaign'])
                ->where('account_id', $campaign->account_id)
                ->where('campaign_id', $campaign->id)
                ->orderByDesc('date')
                ->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param ManualCampaign $campaign
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create(ManualCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        return (new \App\Deluge\Http\Controllers\Insights())
            ->create()
            ->with([
                'account_id'  => $campaign->account_id,
                'campaign_id' => $campaign->id,
                'action'      => route('deluge.campaigns.insights.store', $campaign),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ManualCampaign                            $campaign
     * @param \App\Deluge\Http\Requests\Insights\Create $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(ManualCampaign $campaign, Create $request)
    {
        $this->authorize('update', $campaign);

        (new \App\Deluge\Http\Controllers\Insights())->store($request);

        return redirect()->route('deluge.campaigns.insights.index', $campaign)
            ->with('success', 'Insight was created successfully.');
    }
}
