<?php

namespace App\Http\Controllers;

use App\CRM\Tenant;
use App\Http\Requests\Tenants\Create;
use App\Http\Requests\Tenants\Update;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tenant::class, 'tenant');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            Tenant::query()
                ->searchIn(['name', 'key', 'url'], $request->input('search'))
                ->when(
                    $request->has('all'),
                    fn ($query) => $query->get(),
                    fn ($query) => $query->paginate(),
                )
        );
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
        return response()->json(Tenant::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CRM\Tenant $tenant
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Tenant $tenant)
    {
        return response()->json($tenant->makeVisible(['client_id', 'client_secret', 'api_token']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Tenants\Update $request
     * @param \App\CRM\Tenant                   $tenant
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Tenant $tenant)
    {
        return response()->json(tap($tenant)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CRM\Tenant $tenant
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return response()->noContent();
    }
}
