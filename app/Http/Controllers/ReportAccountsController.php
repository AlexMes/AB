<?php

namespace App\Http\Controllers;

use App\Facebook\Account;
use App\Facebook\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReportAccountsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Account[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Account::visible()
            ->select(['id', 'name'])
            ->searchIn(['id', 'name', 'account_id'], $request->get('search'))
            ->when($request->has('users') && auth()->user()->isAdmin(), function ($query) use ($request) {
                $query->whereIn(
                    'profile_id',
                    Profile::whereIn('user_id', Arr::wrap($request->get('users')))->pluck('id')
                );
            })
            ->orderBy('name')
            ->when($request->get('search'), fn ($query) => $query->get(), fn ($query) => $query->paginate())
            ->makeHidden(['status', 'expenses']);
    }
}
