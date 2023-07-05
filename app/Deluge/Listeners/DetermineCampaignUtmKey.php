<?php

namespace App\Deluge\Listeners;

use App\Deluge\Events\Campaigns\Saving;
use Illuminate\Support\Str;

class DetermineCampaignUtmKey
{
    /**
     * Handle the event.
     *
     * @param Saving $event
     *
     * @return void
     */
    public function handle(Saving $event)
    {
        if (Str::contains($event->campaign->name, 'campaign-')) {
            $event->campaign->utm_key = trim(
                Str::afterLast($event->campaign->name, 'campaign-')
            );
        } else {
            $event->campaign->utm_key = trim(
                Str::afterLast($event->campaign->name, '-')
            );
        }
    }
}
