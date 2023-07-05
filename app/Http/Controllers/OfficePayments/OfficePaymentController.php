<?php

namespace App\Http\Controllers\OfficePayments;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficePayments\Create;
use App\Http\Requests\OfficePayments\Update;
use App\OfficePayment;

class OfficePaymentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OfficePayment::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(OfficePayment::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\OfficePayment $officePayment
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(OfficePayment $officePayment)
    {
        return response()->json($officePayment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update             $request
     * @param \App\OfficePayment $officePayment
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, OfficePayment $officePayment)
    {
        return response()->json(tap($officePayment)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OfficePayment $officePayment
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficePayment $officePayment)
    {
        $officePayment->delete();

        return response()->noContent();
    }
}
