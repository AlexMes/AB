<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\Bundles\Create;
use App\Deluge\Http\Requests\Bundles\Update;
use App\Http\Controllers\Controller;
use App\ManualBundle;
use App\ManualTrafficSource;
use App\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Bundles extends Controller
{
    /**
     * Bundles constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualBundle::class, 'bundle');
    }

    /**
     * Index bundles
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::bundles.index', [
            'offers'    => Offer::allowed()
                ->current()
                ->orderByDesc('id')
                ->get(['id', 'name']),
            'bundles'   => ManualBundle::allowedOffers()
                ->with(['offer', 'trafficSource'])
                ->searchIn(['name'], $request->get('search'))
                ->notEmptyWhereIn('offer_id', Arr::wrap($request->input('offer')))
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
        return view('deluge::bundles.create')->with([
            'offers'   => Offer::allowed()
                ->where('name', 'not like', 'LO_%')
                ->orderByDesc('id')
                ->get(['id', 'name']),
            'trafficSources' => ManualTrafficSource::query()->get(['id', 'name']),
        ]);
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
        ManualBundle::create($request->validated());

        return redirect()->route('deluge.bundles.index')
            ->with('success', 'Bundle was created successfully.');
    }

    /**
     * Show single bundle page
     *
     * @param \App\ManualBundle $bundle
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualBundle $bundle)
    {
        return view('deluge::bundles.show')->with([
            'bundle' => $bundle,
        ]);
    }

    /**
     * Display editing form for bundle
     *
     * @param \App\ManualBundle $bundle
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualBundle $bundle)
    {
        return view('deluge::bundles.edit')->with([
            'bundle'   => $bundle,
            'offers'   => Offer::allowed()
                ->get(['id', 'name']),
            'trafficSources' => ManualTrafficSource::allowed()
                ->get(['id', 'name'])
        ]);
    }

    /**
     * Update bundle details
     *
     * @param \App\ManualBundle                        $bundle
     * @param \App\Deluge\Http\Requests\Bundles\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualBundle $bundle, Update $request)
    {
        $bundle->update($request->validated());

        return redirect()->route('deluge.bundles.show', $bundle);
    }
}
