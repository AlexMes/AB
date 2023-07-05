<?php

namespace App\Listeners;

use App\Events\Offers\Created;

class CopyDataToColdBaseOffer
{
    /**
     * Handle the event.
     *
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if ($event->offer->isColdBase()) {
            $originalOffer = $event->offer->getOriginalColdBaseCopy();

            $event->offer->allowedUsers()->sync($originalOffer->allowedUsers()->pluck('users.id'));
        }
    }
}
