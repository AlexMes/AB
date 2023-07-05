<?php

namespace App\Deluge\Events\Apps;

use App\ManualApp;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Saved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var ManualApp
     */
    public ManualApp $app;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ManualApp $app)
    {
        $this->app = $app;
    }
}
