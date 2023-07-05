<?php

namespace App\Deluge\Listeners;

use App\Deluge\Events\Campaigns\Created;
use App\Deposit;
use App\Lead;

class BindMissedLeads
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
        /** @var Lead $lead */
        foreach (Lead::whereNull('account_id')->whereUtmSource($event->campaign->name)->cursor() as $lead) {
            $lead->update([
                'account_id'  => $event->campaign->account_id,
                'campaign_id' => $event->campaign->id,
            ]);

            if ($lead->account_id !== null) {
                Deposit::whereLeadId($lead->id)->update(['account_id' => $lead->account_id]);
            }
        }
    }
}
