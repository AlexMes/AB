<?php

namespace App\Facebook\Listeners;

use App\Binom\Statistic;
use App\Facebook\Events\Campaigns\CampaignCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttachMissingStatistics implements ShouldQueue
{
    /**
     * Attach missing stats to campaign and user
     *
     * @param \App\Facebook\Events\Campaigns\CampaignCreated $event
     *
     * @return void
     */
    public function handle(CampaignCreated $event)
    {
        Statistic::query()
            ->where('utm_source', $event->campaign->name)
            ->whereNull('fb_campaign_id')
            ->update([
                'fb_campaign_id'    => $event->campaign->id,
                'account_id'        => $event->campaign->account_id,
                'user_id'           => optional(optional($event->campaign->account)->profile)->user_id
            ]);
    }
}
