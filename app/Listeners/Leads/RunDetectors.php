<?php

namespace App\Listeners\Leads;

use App\Events\Lead\Created;

class RunDetectors
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Lead\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        $event->lead->bindBoardAttributes();
    }
}
