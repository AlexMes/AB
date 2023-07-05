<?php

namespace App\Http\Controllers;

use App\AccessSupplier;
use App\Http\Requests\Suppliers\Create;
use App\Http\Requests\Suppliers\Update;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    /**
     * SuppliersController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(AccessSupplier::class, 'supplier');
    }

    /**
     * Load all access suppliers
     *
     * @return \App\AccessSupplier[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        return AccessSupplier::query()
            ->searchIn(['name'], $request->get('search'))
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(AccessSupplier::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\AccessSupplier $supplier
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(AccessSupplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AccessSupplier      $supplier
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, AccessSupplier $supplier)
    {
        return response()->json(tap($supplier)->update($request->validated()), 202);
    }
}
