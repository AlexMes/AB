<?php

namespace App\Facebook\Events\Campaigns;

use App\Facebook\Campaign;

class CampaignSaving
{
    /**
     * Fresh created campaign
     *
     * @var \App\Facebook\Campaign
     */
    public $campaign;

    /**
     * CampaignCreated constructor.
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return void
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }
}
