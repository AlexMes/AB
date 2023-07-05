<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proxies\Create;
use App\Http\Requests\Proxies\Update;
use App\Proxy;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Proxy::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Proxy::query()
            ->with(['branch'])
            ->searchIn(['name'], $request->input('search'))
            ->orderBy('created_at', 'desc')
            ->paginate();
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
        return response()->json(tap(Proxy::create($request->validated()))->loadMissing('branch'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Proxy $proxy
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Proxy $proxy)
    {
        return response()->json($proxy->loadMissing(['branch']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update     $request
     * @param \App\Proxy $proxy
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Proxy $proxy)
    {
        return response()->json(tap($proxy)->update($request->validated())->loadMissing('branch'), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Proxy $proxy
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proxy $proxy)
    {
        $proxy->delete();

        return response()->noContent();
    }
}
