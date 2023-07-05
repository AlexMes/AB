<?php

namespace App\Gamble\Events;

use App\Gamble\GoogleApp;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoogleAppUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Gamble\GoogleApp
     */
    public $app;

    /**
     * Create a new event instance.
     *
     * @param \App\Gamble\GoogleApp $app
     */
    public function __construct(GoogleApp $app)
    {
        $this->app = $app;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(sprintf('App.GoogleApp.%d', $this->app->id));
    }
}
