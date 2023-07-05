<?php

namespace App\Deluge\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\GoogleTwoFactorAuth;
use Illuminate\Http\Request;

class Authenticate extends Controller
{
    use GoogleTwoFactorAuth;

    /**
     * Authenticate user with login and password
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return $this->login($request);
    }

    /**
     * Retrieve redirect path
     *
     * @return string
     */
    public function redirectPath()
    {
        if (auth()->user()->isDeveloper()) {
            return route('deluge.bundles.index');
        }

        if (auth()->user()->isDesigner()) {
            return route('deluge.reports.performance');
        }

        if (auth()->user()->isFinancier()) {
            return route('deluge.credit-cards.index');
        }

        return route('deluge.accounts.index');
    }

    protected function viewName(): string
    {
        return 'deluge::auth.google-tfa';
    }

    protected function routeName(): string
    {
        return 'deluge.google-tfa.login';
    }
}
