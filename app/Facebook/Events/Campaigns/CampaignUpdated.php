<?php

namespace App\Facebook\Events\Campaigns;

use App\Facebook\Campaign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignUpdated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh updated campaign
     *
     * @var \App\Facebook\Campaign
     */
    public $campaign;

    /**
     * CampaignUpdated constructor.
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return void
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Account.{$this->campaign->id}");
    }

    /**
     * Set the event name
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'Updated';
    }
}
