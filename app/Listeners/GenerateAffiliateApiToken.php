<?php

namespace App\Listeners;

use App\Events\AffiliateCreating;
use Illuminate\Support\Str;

class GenerateAffiliateApiToken
{
    /**
     * Handle the event.
     *
     * @param \App\Events\AffiliateCreating $event
     *
     * @return void
     */
    public function handle(AffiliateCreating $event)
    {
        $event->affiliate->api_key = Str::random(230);
    }
}
