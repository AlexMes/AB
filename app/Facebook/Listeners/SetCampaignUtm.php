<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Campaigns\CampaignSaving;
use Illuminate\Support\Str;

class SetCampaignUtm
{
    /**
     * Set utm_key attribute on campaign
     *
     * @param \App\Facebook\Events\Campaigns\CampaignSaving $event
     */
    public function handle(CampaignSaving $event)
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
