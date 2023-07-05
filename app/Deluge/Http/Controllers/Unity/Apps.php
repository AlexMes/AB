<?php

namespace App\Deluge\Http\Controllers\Unity;

use App\Deluge\Http\Requests\Unity\Apps\Create;
use App\Deluge\Http\Requests\Unity\Apps\Update;
use App\Http\Controllers\Controller;
use App\UnityApp;
use App\UnityOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Apps extends Controller
{
    /**
     * Apps constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(UnityApp::class, 'app');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::unity.apps.index', [
            'unityApps' => UnityApp::visible()
                ->with(['organization'])
                ->notEmptyWhereIn('organization_id', Arr::wrap($request->input('organization')))
                ->orderByDesc('created_at')
                ->paginate(),
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
        return view('deluge::unity.apps.create')->with([
            'organizations' => UnityOrganization::visible()->get(['id', 'name']),
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
        $app = UnityApp::create($request->validated());

        return redirect()->route('deluge.unity.apps.show', $app)
            ->with('message', 'App was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\UnityApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(UnityApp $app)
    {
        return view('deluge::unity.apps.show')->with([
            'unityApp' => $app,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\UnityApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(UnityApp $app)
    {
        return view('deluge::unity.apps.edit')->with([
            'unityApp'      => $app,
            'organizations' => UnityOrganization::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update        $request
     * @param \App\UnityApp $app
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, UnityApp $app)
    {
        $app->update($request->validated());

        return redirect()->route('deluge.unity.apps.index')
            ->with('message', 'App was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\UnityApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(UnityApp $app)
    {
        $app->delete();

        return redirect(route('deluge.unity.apps.index'))
            ->with('message', 'App was deleted successfully.');
    }
}
