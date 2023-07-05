<?php

namespace App\Facebook\Events\Profiles;

use App\Facebook\Profile;
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
     * Fresh attached account
     *
     * @var \App\Facebook\Profile
     */
    public $profile;

    /**
     * AccountCreated constructor.
     *
     * @param \App\Facebook\Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile->loadMissing('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Profile.{$this->profile->id}");
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
