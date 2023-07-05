<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\GoogleTwoFactorAuth;

class LoginController extends Controller
{
    use GoogleTwoFactorAuth;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'showTFAForm', 'loginTFA');
    }
}
