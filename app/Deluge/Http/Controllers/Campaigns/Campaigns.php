<?php

namespace App\Deluge\Http\Controllers\Campaigns;

use App\Deluge\Http\Requests\Campaigns\Create;
use App\Deluge\Http\Requests\Campaigns\Update;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualBundle;
use App\ManualCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Campaigns extends Controller
{
    /**
     * Campaigns constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualCampaign::class, 'campaign');
    }

    /**
     * Index campaigns
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::campaigns.index', [
            'bundles'    => ManualBundle::allowedOffers()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
            'campaigns' => ManualCampaign::visible()
                ->allowedOffers()
                ->with(['bundle', 'account'])
                ->searchIn(['name', 'id'], $request->get('search'))
                ->notEmptyWhereIn('bundle_id', Arr::wrap($request->input('bundle')))
                ->orderByDesc('created_at')
                ->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('deluge::campaigns.create')->with([
            'bundles'   => ManualBundle::allowedOffers()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
            'accounts'   => ManualAccount::visible()
                ->get(['account_id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Campaigns\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        ManualCampaign::create($request->validated());

        return redirect()->route('deluge.campaigns.index')
            ->with('success', 'Campaign was created successfully.');
    }

    /**
     * Show single campaign page
     *
     * @param \App\ManualCampaign $campaign
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualCampaign $campaign)
    {
        return view('deluge::campaigns.show')->with([
            'campaign' => $campaign->load(['bundle', 'account']),
        ]);
    }

    /**
     * Display editing form for campaign
     *
     * @param \App\ManualCampaign $campaign
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualCampaign $campaign)
    {
        return view('deluge::campaigns.edit')->with([
            'campaign'   => $campaign->load(['bundle', 'account']),
            'bundles'    => ManualBundle::allowedOffers()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']),
            'accounts'   => ManualAccount::visible()
                ->get(['account_id', 'name']),
        ]);
    }

    /**
     * Update campaign details
     *
     * @param \App\ManualCampaign                        $campaign
     * @param \App\Deluge\Http\Requests\Campaigns\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function update(ManualCampaign $campaign, Update $request)
    {
        $campaign->update($request->validated());

        return redirect()->route('deluge.campaigns.insights.index', $campaign);
    }
}
