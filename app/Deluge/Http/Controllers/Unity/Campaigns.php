<?php

namespace App\Deluge\Http\Controllers\Unity;

use App\Deluge\Http\Requests\Unity\Campaigns\Create;
use App\Deluge\Http\Requests\Unity\Campaigns\Update;
use App\Http\Controllers\Controller;
use App\UnityApp;
use App\UnityCampaign;
use App\UnityOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Campaigns extends Controller
{
    /**
     * Unity campaigns constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(UnityCampaign::class, 'campaign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::unity.campaigns.index', [
            'campaigns' => UnityCampaign::visible()
                ->with(['organization', 'app'])
                ->notEmptyWhereIn('app_id', Arr::wrap($request->input('app')))
                ->when(
                    $request->input('organization'),
                    fn ($query, $input) => $query->whereHas(
                        'app',
                        fn ($q) => $q->whereIn('organization_id', Arr::wrap($input))
                    )
                )
                ->orderByDesc('created_at')
                ->paginate(),
            'unityApps'     => UnityApp::visible()->get(['id', 'name']),
            'organizations' => UnityOrganization::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('deluge::unity.campaigns.create')->with([
            'unityApps' => UnityApp::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $campaign = UnityCampaign::create($request->validated());

        return redirect()->route('deluge.unity.campaigns.show', $campaign)
            ->with('message', 'Campaign was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\UnityCampaign $campaign
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(UnityCampaign $campaign)
    {
        return view('deluge::unity.campaigns.show')->with([
            'campaign' => $campaign,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\UnityCampaign $campaign
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(UnityCampaign $campaign)
    {
        return view('deluge::unity.campaigns.edit')->with([
            'campaign'  => $campaign,
            'unityApps' => UnityApp::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update             $request
     * @param \App\UnityCampaign $campaign
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, UnityCampaign $campaign)
    {
        $campaign->update($request->validated());

        return redirect()->route('deluge.unity.campaigns.index')
            ->with('message', 'Campaign was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\UnityCampaign $campaign
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(UnityCampaign $campaign)
    {
        $campaign->delete();

        return redirect(route('deluge.unity.campaigns.index'))
            ->with('message', 'Campaign was deleted successfully.');
    }
}
