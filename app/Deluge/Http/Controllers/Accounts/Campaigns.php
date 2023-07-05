<?php

namespace App\Deluge\Http\Controllers\Accounts;

use App\Deluge\Http\Requests\Campaigns\Create;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualCampaign;
use Illuminate\Http\Request;

class Campaigns extends Controller
{
    /**
     * Index accounts
     *
     * @param ManualAccount            $account
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\View\View|void
     */
    public function index(ManualAccount $account, Request $request)
    {
        $this->authorize('view', $account);

        return view('deluge::accounts.show', [
            'account'   => $account,
            'campaigns' => ManualCampaign::visible()
                ->allowedOffers()
                ->where('account_id', $account->account_id)
                ->with(['bundle', 'account'])
                ->orderByDesc('created_at')
                ->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param ManualAccount $account
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create(ManualAccount $account)
    {
        $this->authorize('update', $account);

        return (new \App\Deluge\Http\Controllers\Campaigns\Campaigns())
            ->create()
            ->with([
                'account_id' => $account->account_id,
                'action'     => route('deluge.accounts.campaigns.store', $account),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ManualAccount                              $account
     * @param \App\Deluge\Http\Requests\Campaigns\Create $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(ManualAccount $account, Create $request)
    {
        $this->authorize('update', $account);

        (new \App\Deluge\Http\Controllers\Campaigns\Campaigns())->store($request);

        return redirect()->route('deluge.accounts.campaigns.index', $account)
            ->with('success', 'Campaign was created successfully.');
    }
}
