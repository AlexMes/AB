<?php

namespace App\Facebook\Events\Profiles;

use App\Facebook\Profile;
use App\Issue;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Banned implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Banned profile
     *
     * @var \App\Facebook\Profile
     */
    public $profile;

    /**
     * Issue, with ban info
     *
     * @var \App\Issue
     */
    public $issue;

    /**
     * AccountCreated constructor.
     *
     * @param \App\Facebook\Profile $profile
     * @param \App\Issue            $issue
     */
    public function __construct(Profile $profile, Issue $issue = null)
    {
        $this->profile = $profile;
        $this->issue   = $issue;
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
        return 'Banned';
    }
}
