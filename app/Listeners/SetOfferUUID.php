<?php

namespace App\Listeners;

use App\Events\Offers\Creating;
use Illuminate\Support\Str;

class SetOfferUUID
{
    /**
     * Handle the event.
     *
     * @param Creating $event
     *
     * @return void
     */
    public function handle(Creating $event)
    {
        if (empty($event->offer->uuid)) {
            $event->offer->uuid = Str::uuid()->toString();
        }
    }
}
