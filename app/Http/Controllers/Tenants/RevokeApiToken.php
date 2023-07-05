<?php

namespace App\Http\Controllers\Tenants;

use App\CRM\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevokeApiToken extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, Tenant $tenant)
    {
        $this->authorize('update', $tenant);

        $tenant->revokeApiToken();

        return response()->noContent();
    }
}
