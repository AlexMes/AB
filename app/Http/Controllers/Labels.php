<?php

namespace App\Http\Controllers;

use App\CRM\Label;
use Illuminate\Http\Request;

class Labels extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Label[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index()
    {
        return Label::query()->get(['id', 'name']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CRM\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Label $label)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CRM\Label           $label
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Label $label)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CRM\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Label $label)
    {
        //
    }
}
