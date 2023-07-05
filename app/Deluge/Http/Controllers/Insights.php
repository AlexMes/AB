<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\Insights\Create;
use App\Deluge\Http\Requests\Insights\Update;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualCampaign;
use App\ManualInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Insights extends Controller
{
    /**
     * Insights constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualInsight::class, 'insight');
    }

    /**
     * Index insights
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::insights.index', [
            'accounts'    => ManualAccount::query()
                ->visible()
                ->get(['account_id', 'name']),
            'insights' => ManualInsight::allowedOffers()
                ->visible()
                ->with(['account', 'campaign'])
                ->notEmptyWhereIn('account_id', Arr::wrap($request->input('account')))
                ->orderByDesc('date')
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
        return view('deluge::insights.create')->with([
            'accounts'   => ManualAccount::query()
                ->visible()
                ->get(['account_id', 'name']),
            'campaigns'   => ManualCampaign::allowedOffers()
                ->where('created_at', '>', now()->subMonths(2)->toDateTimeString())
                ->visible()
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Insights\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Create $request)
    {
        ManualInsight::create($request->validated());

        return redirect()->route('deluge.insights.index')
            ->with('success', 'Insight was created successfully.');
    }

    /**
     * Show single insight page
     *
     * @param \App\ManualInsight $insight
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualInsight $insight)
    {
        return view('deluge::insights.show')->with([
            'insight' => $insight->load(['account', 'campaign']),
        ]);
    }

    /**
     * Display editing form for insight
     *
     * @param \App\ManualInsight $insight
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualInsight $insight)
    {
        return view('deluge::insights.edit')->with([
            'insight'    => $insight->load(['account', 'campaign']),
            'accounts'   => ManualAccount::query()
                ->visible()
                ->get(['account_id', 'name']),
            'campaigns'   => ManualCampaign::allowedOffers()
                ->where('created_at', '>', now()->subMonths(2)->toDateTimeString())
                ->visible()
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Update insight details
     *
     * @param \App\ManualInsight                        $insight
     * @param \App\Deluge\Http\Requests\Insights\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualInsight $insight, Update $request)
    {
        $insight->update($request->validated());

        return redirect()->route('deluge.insights.show', $insight);
    }
}
