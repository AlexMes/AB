<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RedirectToCrm extends Controller
{
    /**
     * Redirect user to TCRM auth
     *
     * @param \App\CRM\Tenant          $tenant
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function __invoke(Tenant $tenant, Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id'     => $tenant->client_id,
            'redirect_uri'  => route('crm.auth.callback', $tenant),
            'response_type' => 'code',
            'scope'         => ['email'],
            'state'         => $state,
        ]);

        return redirect(sprintf('%s/oauth/authorize?%s', $tenant->url, $query));
    }
}
