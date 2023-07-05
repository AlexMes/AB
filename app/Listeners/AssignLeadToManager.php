<?php

namespace App\Listeners;

use App\AdsBoard;
use App\Events\Lead\Created;
use App\LeadAssigner\LeadAssigner;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignLeadToManager implements ShouldQueue
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
        LeadAssigner::dispatch($event->lead->refresh())->delay(now()->addSeconds(30))->onQueue(AdsBoard::QUEUE_DEFAULT);
    }
}
