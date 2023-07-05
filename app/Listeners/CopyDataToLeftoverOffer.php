<?php

namespace App\Listeners;

use App\Events\Offers\Created;
use App\Offer;
use Illuminate\Support\Str;

class CopyDataToLeftoverOffer
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
        if ($event->offer->isLeftover()) {
            $parentOffer = Offer::firstWhere('name', Str::after($event->offer->name, 'LO_'));

            $event->offer->update([
                'vertical' => optional($parentOffer)->vertical,
            ]);

            $event->offer->allowedUsers()->sync($parentOffer->allowedUsers()->pluck('users.id'));
        }
    }
}
