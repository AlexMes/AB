<?php

namespace App\Listeners;

use App\Events\Lead\Updated;
use App\Jobs\Leads\FetchIpAddressData;

class PullLeadIpInfo
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Updated $event)
    {
        if ($event->lead->isDirty('ip')) {
            try {
                FetchIpAddressData::dispatchNow($event->lead);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
