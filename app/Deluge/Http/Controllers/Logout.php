<?php

namespace App\Deluge\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Logout extends Controller
{
    /**
     * Log user out
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        auth('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
