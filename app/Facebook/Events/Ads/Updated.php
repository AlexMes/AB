<?php

namespace App\Facebook\Events\Ads;

use App\Facebook\Ad;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh attached ads account
     *
     * @var Ad
     */
    public $ad;

    /**
     * AccountCreated constructor.
     *
     * @param Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Account.{$this->ad->id}");
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
