<?php

namespace App\Http\Controllers;

use App\Facebook\Account;
use App\Facebook\Profile;
use App\Http\Requests\Accounts\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AccountsController extends Controller
{
    /**
     * List all visible ad accounts
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        return Account::visible()
            ->with(['group'])
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->searchIn(['id', 'name', 'account_id'], $request->get('search'))
            ->when($request->has('users') && auth()->user()->isAdmin(), function ($query) use ($request) {
                $query->whereIn(
                    'profile_id',
                    Profile::whereIn('user_id', Arr::wrap($request->get('users')))->pluck('id')
                );
            })
            ->when($request->get('active'), fn ($q) => $q->whereNull('banned_at'))
            ->notEmptyWhereIn('profile_id', Arr::wrap($request->get('profiles')))
            ->notEmptyWhereIn('group_id', Arr::wrap($request->get('group')))
            ->when($request->has('since'), fn ($q) => $q->whereBetween('created_at', [
                Carbon::parse($request->since)->startOfDay()->toDateTimeString(),
                Carbon::parse($request->until)->endOfDay()->toDateTimeString()
            ]))
            ->orderByRaw('spend DESC NULLS LAST')
            ->unless(
                $request->get('all'),
                fn ($query) => $query->with(['profile:id,name'])->paginate(),
                fn ($query) => $query->get()
            );
    }

    /**
     * Load single ad account data
     *
     * @param \App\Facebook\Account $account
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \App\Facebook\Account
     */
    public function show(Account $account)
    {
        $this->authorize('view', $account);

        return $account;
    }

    /**
     * Update account information
     *
     * @param \App\Facebook\Account              $account
     * @param \App\Http\Requests\Accounts\Update $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Account $account, Update $request)
    {
        return response()->json(
            tap($account)->update($request->validated()),
            202
        );
    }
}
