<?php

namespace App\Integrations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Form;
use App\Integrations\Http\Requests\CreateFormRequest;
use App\Integrations\Http\Requests\UpdateFormRequest;
use Illuminate\Http\Request;

class FormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Form::query()
            ->searchIn(['name'], $request->search)
            ->orderBy('id')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateFormRequest $request
     *
     * @return Form
     */
    public function store(CreateFormRequest $request)
    {
        return Form::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param Form $form
     *
     * @return Form
     */
    public function show(Form $form)
    {
        return $form;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFormRequest $request
     * @param Form              $form
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateFormRequest $request, Form $form)
    {
        return response()->json(tap($form)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Form $form
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Form $form)
    {
        return response('Forbidden', 403);
    }
}
