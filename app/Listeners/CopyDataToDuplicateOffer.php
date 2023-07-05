<?php

namespace App\Listeners;

use App\Events\Offers\Created;

class CopyDataToDuplicateOffer
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
        if ($event->offer->isDuplicate()) {
            $originalOffer = $event->offer->getOriginalDuplicateCopy();

            $event->offer->update([
                'vertical' => optional($originalOffer)->vertical,
            ]);

            $event->offer->allowedUsers()->sync($originalOffer->allowedUsers()->pluck('users.id'));
        }
    }
}
