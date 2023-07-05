<?php

namespace App\Facebook\Events\Campaigns;

use App\Facebook\Campaign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignCreated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

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
