<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\TrafficSources\Create;
use App\Deluge\Http\Requests\TrafficSources\Update;
use App\Http\Controllers\Controller;
use App\ManualTrafficSource;
use Illuminate\Http\Request;

class TrafficSources extends Controller
{
    /**
     * Index bundles
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ManualTrafficSource::class);

        return view('deluge::traffic-sources.index', [
            'trafficSources' => ManualTrafficSource::query()
                ->searchIn(['name'], $request->get('search'))
                ->orderBy('created_at')
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
        $this->authorize('create', ManualTrafficSource::class);

        return view('deluge::traffic-sources.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Bundles\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $this->authorize('create', ManualTrafficSource::class);

        ManualTrafficSource::create($request->validated());

        return redirect()->route('deluge.traffic-sources.index')
            ->with('success', 'Traffic source was created successfully.');
    }

    /**
     * Show single bundle page
     *
     * @param \App\ManualTrafficSource $trafficSource
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualTrafficSource $trafficSource)
    {
        $this->authorize('view', ManualTrafficSource::class);

        return view('deluge::traffic-sources.show')->with([
            'trafficSource' => $trafficSource,
        ]);
    }

    /**
     * Display editing form for traffic source
     *
     * @param \App\ManualTrafficSource $trafficSource
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(ManualTrafficSource $trafficSource)
    {
        $this->authorize('update', ManualTrafficSource::class);

        return view('deluge::traffic-sources.edit')->with([
            'trafficSource'   => $trafficSource,
        ]);
    }

    /**
     * Update bundle details
     *
     * @param \App\ManualTrafficSource                 $trafficSource
     * @param \App\Deluge\Http\Requests\Bundles\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualTrafficSource $trafficSource, Update $request)
    {
        $this->authorize('update', ManualTrafficSource::class);

        $trafficSource->update($request->validated());

        return redirect()->route('deluge.traffic-sources.show', $trafficSource);
    }
}
