<?php

namespace App\Gamble\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RenderApplication extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('gamble::app');
    }
}
