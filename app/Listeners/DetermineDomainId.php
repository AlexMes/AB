<?php

namespace App\Listeners;

use App\Facebook\Events\Ads\Saved;
use App\Jobs\DetectDomainIdForAd;

class DetermineDomainId
{
    /**
     * Handle the event.
     *
     * @param \App\Facebook\Events\Ads\Saved $event
     *
     * @return void
     */
    public function handle(Saved $event)
    {
        DetectDomainIdForAd::dispatchNow($event->ad);
    }
}
