<?php

namespace App\Http\Controllers\External;

use App\Facebook\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\External\AccountList as ExternalAccountList;
use App\Http\Resources\ExternalAccount;

class AccountList extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\External\AccountList $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function __invoke(ExternalAccountList $request)
    {
        return ExternalAccount::collection(Account::query()
            ->select([
                'id',
                'account_id',
                'name',
            ])
            ->searchIn(['id','name'], $request->input('search'))
            ->whereHas(
                'cachedInsights',
                fn ($query) => $query->whereBetween('date', [$request->input('since') ?? now(), $request->input('until') ?? now()])
            )
            ->withSpendForPeriod($request->input('since'), $request->input('until'))
            ->withLifetime()
            ->orderBy(
                $request->input('orderBy', 'spend'),
                $request->input('dir', 'desc')
            )
            ->paginate($request->get('perPage', 100)));
    }
}
