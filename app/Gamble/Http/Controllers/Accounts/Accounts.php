<?php

namespace App\Gamble\Http\Controllers\Accounts;

use App\Gamble\Account;
use App\Gamble\Http\Requests\Accounts\Create;
use App\Gamble\Http\Requests\Accounts\Update;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Accounts extends Controller
{
    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Account::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Account::visible()
            ->with(['user'])
            ->orderByDesc('created_at')
            ->when(
                $request->has('all'),
                fn ($query) => $query->get(),
                fn ($query) => $query->paginate()
            );
    }

    /**
     * @param Account $account
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Account $account)
    {
        return response()->json($account->load(['user', 'groups']));
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        $account = Account::create(Arr::except($request->validated(), ['group_id']));

        $account->groups()->attach($request->input('group_id', []));

        return response()->json($account->load(['user', 'groups']), 201);
    }

    /**
     * @param Account $account
     * @param Update  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Account $account, Update $request)
    {
        $account->update(Arr::except($request->validated(), ['group_id']));

        $account->groups()->sync($request->input('group_id', []));

        return response()->json($account->fresh(['user', 'groups']), 202);
    }
}
