<?php

namespace App\Deluge\Http\Controllers\Unity;

use App\Deluge\Http\Requests\Unity\Organizations\Create;
use App\Deluge\Http\Requests\Unity\Organizations\Update;
use App\Http\Controllers\Controller;
use App\UnityOrganization;
use Illuminate\Http\Request;

class Organizations extends Controller
{
    /**
     * Apps constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(UnityOrganization::class, 'organization');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::unity.organizations.index', [
            'organizations' => UnityOrganization::visible()
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
        return view('deluge::unity.organizations.create');
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
        $organization = UnityOrganization::create($request->validated());

        return redirect()->route('deluge.unity.organizations.show', $organization)
            ->with('message', 'Organization was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\UnityOrganization $organization
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(UnityOrganization $organization)
    {
        return view('deluge::unity.organizations.show')->with([
            'organization' => $organization,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\UnityOrganization $organization
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(UnityOrganization $organization)
    {
        return view('deluge::unity.organizations.edit')->with([
            'organization'   => $organization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update                 $request
     * @param \App\UnityOrganization $organization
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, UnityOrganization $organization)
    {
        $organization->update($request->validated());

        return redirect()->route('deluge.unity.organizations.index', $organization)
            ->with('message', 'Organization was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\UnityOrganization $organization
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(UnityOrganization $organization)
    {
        $organization->delete();

        return redirect(route('deluge.unity.organizations.index'))
            ->with('message', 'Organization was deleted successfully.');
    }
}
