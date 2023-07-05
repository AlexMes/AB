<?php

namespace App\Listeners;

use App\Facebook\Events\Ads\Saved;
use App\Jobs\DetectDesignerIdForAd;

class DetermineDesignerId
{
    /**
     * Handle the event.
     *
     * @param Saved $event
     *
     * @return void
     */
    public function handle(Saved $event)
    {
        DetectDesignerIdForAd::dispatchNow($event->ad);
    }
}
