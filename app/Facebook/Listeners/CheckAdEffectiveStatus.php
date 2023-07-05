<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Ads\Saved;
use App\Facebook\Jobs\RecountAdsForDomain;

class CheckAdEffectiveStatus
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
        if ($event->ad->wasChanged('effective_status') && $event->ad->hasDomain()) {
            RecountAdsForDomain::dispatchNow($event->ad->domain);
        }
    }
}
