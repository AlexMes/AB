<?php

namespace App\Http\Controllers\Campaigns;

use App\Facebook\Campaign;
use App\Http\Controllers\Controller;

class Start extends Controller
{
    /**
     * Start campaign on Facebook
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return \FacebookAds\ApiRequest|\FacebookAds\Cursor|\FacebookAds\Http\ResponseInterface|null
     */
    public function __invoke(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        return $campaign->start();
    }
}
