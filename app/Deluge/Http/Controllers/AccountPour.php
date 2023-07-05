<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\AccountPour\Update as Update;
use App\Http\Controllers\Controller;
use App\ManualAccountManualPour;

class AccountPour extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param ManualAccountManualPour                      $pivot
     * @param \App\Deluge\Http\Requests\AccountPour\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(ManualAccountManualPour $pivot, Update $request)
    {
        $pivot->update($request->validated());
        $pivot->account->update($request->validated());

        return back();
    }
}
