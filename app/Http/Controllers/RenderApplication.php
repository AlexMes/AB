<?php

namespace App\Http\Controllers;

class RenderApplication extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function __invoke()
    {
        return view('application');
    }
}
