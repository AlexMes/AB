<?php

namespace App\Listeners;

use App\Facebook\Campaign;
use App\Facebook\Events\Campaigns\CampaignSaving;
use App\Offer;
use Illuminate\Support\Str;

class RunOfferDetection
{
    /**
     * Handle the event.
     *
     * @param \App\Facebook\Events\Campaigns\CampaignCreated $event
     *
     * @return void
     */
    public function handle(CampaignSaving $event)
    {
        $event->campaign->offer_id = $this->findOffer($event->campaign);
    }

    /**
     * Get offer id from campaign
     *
     * @return void
     */
    protected function findOffer(Campaign $campaign)
    {
        return Offer::all(['id','name'])
            ->sortByDesc(fn ($offer, $key) => strlen($offer['name']))
            ->filter(fn ($offer) => Str::contains(Str::lower($campaign->name), sprintf("-%s-", Str::lower($offer->name))))
            ->first()->id ?? null;
    }
}
