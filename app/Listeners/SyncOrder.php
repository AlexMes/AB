<?php

namespace App\Listeners;

use App\Events\Domains\Saved;
use App\Jobs\UpdateOrderProgress;

class SyncOrder
{
    /**
     * @param \App\Events\Domains\Saved $event
     */
    public function handle(Saved $event)
    {
        if ($event->domain->isDirty('status')) {
            UpdateOrderProgress::dispatch($event->domain);
        }
    }
}
