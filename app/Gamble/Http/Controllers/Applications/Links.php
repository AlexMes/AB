<?php

namespace App\Gamble\Http\Controllers\Applications;

use App\Gamble\GoogleApp;
use App\Gamble\GoogleAppLink;
use App\Gamble\Http\Requests\Applications\CreateLink;
use App\Gamble\Http\Requests\Applications\UpdateLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Links extends Controller
{
    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(GoogleAppLink::class, 'link');
    }

    /**
     * Display a listing of the resource.
     *
     * @param GoogleApp $application
     * @param Request   $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(GoogleApp $application, Request $request)
    {
        return response()->json(
            $application->links()
                ->with(['user'])
                ->orderByDesc('created_at')
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GoogleApp                                         $application
     * @param \App\Gamble\Http\Requests\Applications\CreateLink $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GoogleApp $application, CreateLink $request)
    {
        return response()->json($application->links()->create($request->validated())->load(['user']), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GoogleApp                                         $application
     * @param \App\Gamble\GoogleAppLink                         $link
     * @param \App\Gamble\Http\Requests\Applications\UpdateLink $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GoogleApp $application, GoogleAppLink $link, UpdateLink $request)
    {
        return response()->json(tap($link)->update($request->validated())->load(['user']), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param GoogleApp                 $application
     * @param \App\Gamble\GoogleAppLink $link
     *
     * @return void
     */
    public function destroy(GoogleApp $application, GoogleAppLink $link)
    {
        //
    }
}
