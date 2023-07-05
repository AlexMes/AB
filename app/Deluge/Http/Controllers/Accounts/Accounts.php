<?php

namespace App\Deluge\Http\Controllers\Accounts;

use App\Deluge\Http\Requests\Accounts\Create;
use App\Deluge\Http\Requests\Accounts\Update;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualGroup;
use App\ManualSupplier;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Accounts extends Controller
{
    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualAccount::class, 'account');
    }

    /**
     * Index accounts
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::accounts.index', [
            'users'    => User::visible()->withFacebookTraffic()->get(['id', 'name']),
            'groups'   => ManualGroup::visible()->get(['id', 'name']),
            'accounts' => ManualAccount::visible()
                ->withSpend()
                ->with(['user'])
                ->searchIn(['name', 'account_id'], $request->get('search'))
                ->notEmptyWhereIn('user_id', Arr::wrap($request->input('user')))
                ->notEmptyWhereIn('moderation_status', Arr::wrap($request->input('moderation_status')))
                ->when(Arr::wrap($request->input('group')), function ($query, $input) {
                    return $query->whereHas('groups', fn ($q) => $q->whereIn('manual_groups.id', $input));
                })
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
        return view('deluge::accounts.create')->with([
            'users'     => User::visible()->withFacebookTraffic()->get(['id', 'name']),
            'groups'    => ManualGroup::visible()->orderBy('name')->get(['id', 'name']),
            'suppliers' => ManualSupplier::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Accounts\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $account = ManualAccount::create(Arr::except($request->validated(), ['group_id']));
        $account->groups()->sync($request->input('group_id', []));

        return redirect()->route('deluge.accounts.campaigns.index', $account)
            ->with('success', 'Account was created successfully.');
    }

    /**
     * Show single account page
     *
     * @param \App\ManualAccount $account
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualAccount $account)
    {
        return view('deluge::accounts.show')->with([
            'account' => $account,
        ]);
    }

    /**
     * Display editing form for account
     *
     * @param \App\ManualAccount $account
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualAccount $account)
    {
        return view('deluge::accounts.edit')->with([
            'account'   => $account,
            'users'     => User::visible()->withFacebookTraffic()->get(['id', 'name']),
            'groups'    => ManualGroup::visible()->orderBy('name')->get(['id', 'name']),
            'suppliers' => ManualSupplier::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Update account details
     *
     * @param \App\ManualAccount                        $account
     * @param \App\Deluge\Http\Requests\Accounts\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualAccount $account, Update $request)
    {
        $account->update(Arr::except($request->validated(), ['group_id']));
        $account->groups()->sync($request->input('group_id', []));

        return redirect()->route('deluge.accounts.campaigns.index', $account);
    }
}
