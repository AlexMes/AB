<?php

namespace App\Facebook\Listeners;

use App\Events\AdDisApproved;

class LogAdStatus
{
    /**
     * Handle the event.
     *
     * @param AdDisApproved $event
     *
     * @return void
     */
    public function handle(AdDisApproved $event)
    {
        $event->ad->disapprovals()->create([
            'reason' => $event->ad->reject_reason,
        ]);
    }
}
