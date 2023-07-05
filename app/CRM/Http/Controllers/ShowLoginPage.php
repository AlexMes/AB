<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Tenant;
use App\Http\Controllers\Controller;

class ShowLoginPage extends Controller
{
    /**
     * ShowLoginPage constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:crm');
    }

    /**
     * Show login page for CRM module
     *
     * @param \App\CRM\Tenant $tenant
     *
     * @return \Illuminate\View\View
     */
    public function __invoke(Tenant $tenant)
    {
        return view('crm::auth.login')->withTenant($tenant);
    }
}
