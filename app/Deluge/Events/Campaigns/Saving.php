<?php

namespace App\Deluge\Events\Campaigns;

use App\ManualCampaign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Saving
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var ManualCampaign
     */
    public ManualCampaign $campaign;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ManualCampaign $campaign)
    {
        $this->campaign = $campaign;
    }
}
