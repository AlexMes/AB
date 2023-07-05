<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\Apps\Create;
use App\Deluge\Http\Requests\Apps\Update;
use App\Http\Controllers\Controller;
use App\ManualApp;
use Illuminate\Http\Request;

class Apps extends Controller
{
    /**
     * Apps constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualApp::class, 'app');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::apps.index', [
            'apps' => ManualApp::visible()
                ->notEmptyWhere('status', $request->input('status'))
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
        return view('deluge::apps.create');
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
        $app = ManualApp::create($request->validated());

        return redirect()->route('deluge.apps.show', $app)
            ->with('success', 'App was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ManualApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(ManualApp $app)
    {
        return view('deluge::apps.show')->with([
            'app' => $app,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ManualApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(ManualApp $app)
    {
        return view('deluge::apps.edit')->with([
            'app'   => $app,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update         $request
     * @param \App\ManualApp $app
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, ManualApp $app)
    {
        $app->update($request->validated());

        return redirect()->route('deluge.apps.index', $app);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ManualApp $app
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(ManualApp $app)
    {
        $app->delete();

        return redirect(route('deluge.apps.index'))
            ->with('message', 'App was successfully deleted.');
    }
}
