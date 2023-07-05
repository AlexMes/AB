<?php

namespace App\Gamble\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowLoginPage extends Controller
{
    /**
     * ShowLoginPage constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:gamble');
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('gamble::auth.login');
    }
}
