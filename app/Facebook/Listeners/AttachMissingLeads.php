<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Campaigns\CampaignCreated;
use App\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttachMissingLeads implements ShouldQueue
{
    /**
     * Attach missing leads to campaign and account
     *
     * @param \App\Facebook\Events\Campaigns\CampaignCreated $event
     *
     * @return void
     */
    public function handle(CampaignCreated $event)
    {
        Lead::whereNull('campaign_id')
            ->where('utm_source', $event->campaign->name)
            ->get()
            ->each(function (Lead $lead) use ($event) {
                $lead->update(['campaign_id' => $event->campaign->id]);

                $lead->bindBoardAttributes();
            });
    }
}
