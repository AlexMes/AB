<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use App\Jobs\Leads\FetchIpAddressData;

class PullIpInfo
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        try {
            FetchIpAddressData::dispatchNow($event->lead);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
