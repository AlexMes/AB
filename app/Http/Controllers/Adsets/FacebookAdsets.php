<?php

namespace App\Http\Controllers\Adsets;

use App\Facebook\AdSet;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adsets\FacebookUpdate;

class FacebookAdsets extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param FacebookUpdate $request
     * @param AdSet          $adset
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(FacebookUpdate $request, AdSet $adset)
    {
        return $adset->updateBudget(digits($request->budget) * 100, $request->budget_field);
    }
}
