<?php

namespace App\Http\Controllers\Campaigns;

use App\Facebook\Campaign;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaigns\FacebookUpdate;

class FacebookCampaigns extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Campaigns\FacebookUpdate $request
     * @param \App\Facebook\Campaign                      $campaign
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(FacebookUpdate $request, Campaign $campaign)
    {
        return $campaign->updateBudget(digits($request->budget) * 100, $request->budget_field);
    }
}
