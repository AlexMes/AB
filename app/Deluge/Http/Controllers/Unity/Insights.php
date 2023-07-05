<?php

namespace App\Deluge\Http\Controllers\Unity;

use App\Deluge\Http\Requests\Unity\Insights\Create;
use App\Deluge\Http\Requests\Unity\Insights\Update;
use App\Http\Controllers\Controller;
use App\UnityApp;
use App\UnityCampaign;
use App\UnityInsight;
use App\UnityOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Insights extends Controller
{
    /**
     * Unity insights constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(UnityInsight::class, 'insight');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::unity.insights.index', [
            'insights' => UnityInsight::visible()
                ->with(['organization', 'app', 'campaign'])
                ->notEmptyWhereIn('app_id', Arr::wrap($request->input('app')))
                ->notEmptyWhereIn('campaign_id', Arr::wrap($request->input('campaign')))
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
            'campaigns'     => UnityCampaign::visible()->get(['id', 'name']),
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
        return view('deluge::unity.insights.create')->with([
            'unityApps' => UnityApp::visible()->get(['id', 'name']),
            'campaigns' => UnityCampaign::visible()->get(['id', 'name']),
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
        $insight = UnityInsight::create($request->validated());

        return redirect()->route('deluge.unity.insights.show', $insight)
            ->with('message', 'Insight was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\UnityInsight $insight
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(UnityInsight $insight)
    {
        return view('deluge::unity.insights.show')->with([
            'insight' => $insight,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\UnityInsight $insight
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(UnityInsight $insight)
    {
        return view('deluge::unity.insights.edit')->with([
            'insight'   => $insight,
            'unityApps' => UnityApp::visible()->get(['id', 'name']),
            'campaigns' => UnityCampaign::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update            $request
     * @param \App\UnityInsight $insight
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, UnityInsight $insight)
    {
        $insight->update($request->validated());

        return redirect()->route('deluge.unity.insights.index')
            ->with('message', 'Insight was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\UnityInsight $insight
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(UnityInsight $insight)
    {
        $insight->delete();

        return redirect(route('deluge.unity.insights.index'))
            ->with('message', 'Insight was deleted successfully.');
    }
}
