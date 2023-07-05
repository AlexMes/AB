<?php

namespace App\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerLocale extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     *
     */
    public function __invoke(Request $request)
    {
        if (auth('crm')->check()) {
            auth('crm')->user()->update(['locale' => $request->input('locale')]);
        }

        return back();
    }
}
