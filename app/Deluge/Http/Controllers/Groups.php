<?php

namespace App\Deluge\Http\Controllers;

use App\Branch;
use App\Deluge\Http\Requests\Groups\Create;
use App\Deluge\Http\Requests\Groups\Update;
use App\Deluge\Reports\GroupStats\Report;
use App\Http\Controllers\Controller;
use App\ManualGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Groups extends Controller
{
    /**
     * Groups constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualGroup::class, 'group');
    }

    /**
     * Index groups
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::groups.index', [
            'groups'   => ManualGroup::visible()
                ->with(['branch'])
                ->searchIn(['name'], $request->get('search'))
                ->orderByDesc('name')
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
        return view('deluge::groups.create', [
            'branches' => Branch::allowed()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Groups\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        ManualGroup::create($request->validated());

        return redirect()->route('deluge.groups.index')
            ->with('success', 'Group was created successfully.');
    }

    /**
     * Show single group page
     *
     * @param \App\ManualGroup $group
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualGroup $group, Request $request)
    {
        return view('deluge::groups.show')->with([
            'group'    => $group->loadCount('accounts')->loadMissing(['branch']),
            'report'   => (new Report([
                'since'     => $request->input('since'),
                'until'     => $request->input('until'),
                'groups'    => [$group->id],
                'hide_cols' => ['name'],
            ]))->toArray(),
            'accounts' => $group->accounts()
                ->with(['user'])
                ->whereHas('pours', function (Builder $query) use ($request) {
                    return $query->whereBetween('manual_pours.date', [
                        Carbon::parse($request->input('since', now()->startOfMonth())),
                        Carbon::parse($request->input('until')),
                    ]);
                })
                ->withSpend()
                ->orderByDesc('created_at')
                ->paginate(50),
        ]);
    }

    /**
     * Display editing form for group
     *
     * @param \App\ManualGroup $group
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualGroup $group)
    {
        return view('deluge::groups.edit')->with([
            'group'    => $group,
            'branches' => Branch::allowed()->get(['id', 'name']),
        ]);
    }

    /**
     * Update group details
     *
     * @param \App\ManualGroup                        $group
     * @param \App\Deluge\Http\Requests\Groups\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualGroup $group, Update $request)
    {
        $group->update($request->validated());

        return redirect()->route('deluge.groups.show', $group);
    }
}
