<?php

namespace App\Gamble\Http\Controllers\Applications;

use App\Gamble\GoogleApp;
use App\Gamble\Http\Requests\Applications\Create;
use App\Gamble\Http\Requests\Applications\Update;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Applications extends Controller
{
    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(GoogleApp::class, 'application');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return GoogleApp::query()
            ->orderByDesc('created_at')
            ->when(
                $request->has('all'),
                fn ($query) => $query->get(['id', 'name']),
                fn ($query) => $query->paginate()
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Gamble\Http\Requests\Applications\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        return response()->json(GoogleApp::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Gamble\GoogleApp $application
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(GoogleApp $application)
    {
        return response()->json($application);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Gamble\Http\Requests\Applications\Update $request
     * @param \App\Gamble\GoogleApp                         $application
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, GoogleApp $application)
    {
        return response()->json(tap($application)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Gamble\GoogleApp $application
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleApp $application)
    {
        //
    }
}
