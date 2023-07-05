<?php

namespace App\Deluge\Http\Controllers;

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
        $this->middleware('guest:web');
    }

    /**
     * Show login page for Deluge module
     *
     * @return \Illuminate\View\View
     */
    public function __invoke()
    {
        return view('deluge::auth.login');
    }
}
