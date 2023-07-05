<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\ToggleRevenueVisibility;
use App\Http\Controllers\Controller;

class RevenueVisibility extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param ToggleRevenueVisibility $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __invoke(ToggleRevenueVisibility $request)
    {
        auth()->user()->toggleRevenueVisibility();

        return back();
    }
}
