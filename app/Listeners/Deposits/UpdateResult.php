<?php

namespace App\Listeners\Deposits;

use App\Events\Deposits\Created;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateResult implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Deposits\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        $event->deposit->updateCorrespondingResult();
    }
}
