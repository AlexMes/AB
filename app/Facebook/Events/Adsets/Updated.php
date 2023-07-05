<?php

namespace App\Facebook\Events\Adsets;

use App\Facebook\Account;
use App\Facebook\AdSet;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh attached adset
     *
     * @var AdSet
     */
    public $adset;

    /**
     * AdsetCreated constructor.
     *
     * @param Account $account
     */
    public function __construct(AdSet $adset)
    {
        $this->adset = $adset;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Adset.{$this->adset->id}");
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
