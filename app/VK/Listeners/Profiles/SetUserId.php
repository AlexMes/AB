<?php

namespace App\VK\Listeners\Profiles;

use App\VK\Events\Profiles\Creating;

class SetUserId
{
    /**
     * Handle the event.
     *
     * @param \App\VK\Events\Profiles\Creating $event
     *
     * @return void
     */
    public function handle(Creating $event)
    {
        if (auth()->check() && $event->profile->user_id === null) {
            $event->profile->user_id = auth()->id();
        }
    }
}
