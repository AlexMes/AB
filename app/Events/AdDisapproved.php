<?php

namespace App\Events;

use App\Facebook\Ad;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdDisapproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Facebook\Ad
     */
    public Ad $ad;

    /**
     * Create a new event instance.
     *
     * @param \App\Facebook\Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }
}
