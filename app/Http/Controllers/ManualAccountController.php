<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManualAccounts\Create;
use App\Http\Requests\ManualAccounts\Update;
use App\ManualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ManualAccountController extends Controller
{

    /**
     * ManualAccountController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(ManualAccount::class, 'manual_account');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ManualAccount::query()
            ->visible()
            ->with(['user'])
            ->searchIn(['name', 'account_id'], $request->input('search'))
            ->notEmptyWhereIn('user_id', Arr::wrap($request->input('user_id')))
            ->orderByDesc('created_at')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ManualAccounts\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(ManualAccount::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ManualAccount $manualAccount
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(ManualAccount $manualAccount)
    {
        return response()->json($manualAccount->load(['user']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ManualAccounts\Update $request
     * @param \App\ManualAccount                       $manualAccount
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, ManualAccount $manualAccount)
    {
        return response()->json(tap($manualAccount)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ManualAccount $manualAccount
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManualAccount $manualAccount)
    {
        $manualAccount->delete();

        return response()->noContent();
    }
}
