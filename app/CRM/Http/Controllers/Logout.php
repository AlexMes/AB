<?php

namespace App\CRM\Http\Controllers;

use App\Http\Controllers\Controller;

class Logout extends Controller
{
    /**
     * Log user out
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke()
    {
        $tenant = auth('crm')->user();

        auth('crm')->logout();
        auth('web')->logout();

        if ($tenant !== null && $tenant->hasTenant()) {
            return redirect()->route('crm.login', $tenant->tenant);
        }

        return redirect('/login');
    }
}
