<?php

namespace App\Http\Controllers;

use App\GoogleApp;
use App\Http\Requests\CreateGoogleApp;
use App\Http\Requests\UpdateGoogleApp;

class GoogleAppController extends Controller
{
    /**
     * GoogleAppController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(GoogleApp::class);
    }

    /**
     * List all apps
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return GoogleApp::query()->orderByDesc('id')->paginate();
    }

    /**
     * Load single app info
     *
     * @param \App\GoogleApp $app
     *
     * @return \App\GoogleApp
     */
    public function show(GoogleApp $app)
    {
        return $app;
    }

    /**
     * Create an app
     *
     * @param \App\Http\Requests\CreateGoogleApp $request
     *
     * @return \App\GoogleApp|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store(CreateGoogleApp $request)
    {
        return GoogleApp::query()->create($request->validated());
    }

    /**
     * Update app
     *
     * @param \App\GoogleApp                     $app
     * @param \App\Http\Requests\UpdateGoogleApp $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GoogleApp $app, UpdateGoogleApp $request)
    {
        return response()->json(tap($app)->update($request->validated()), 202);
    }
}
