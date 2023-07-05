<?php

namespace App\Http\Controllers;

use App\BlackLead;
use App\Http\Requests\CreateBlackLead;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BlackLeadController extends Controller
{
    /**
     * Branch constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(BlackLead::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return BlackLead[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return BlackLead::query()
            ->when($request->input('branch_id'), fn ($q, $input) => $q->whereIn('branch_id', Arr::wrap($input)))
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateBlackLead $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateBlackLead $request)
    {
        $blackLead = BlackLead::create($request->validated());

        return response()->json($blackLead, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\BlackLead $blackLead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlackLead $blackLead)
    {
        $blackLead->delete();

        return response()->noContent();
    }
}
