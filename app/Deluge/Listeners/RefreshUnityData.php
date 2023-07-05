<?php

namespace App\Deluge\Listeners;

use App\Deluge\Events\Unity\Organizations\Created;

class RefreshUnityData
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
        $event->organization->refreshUnityData();
    }
}
