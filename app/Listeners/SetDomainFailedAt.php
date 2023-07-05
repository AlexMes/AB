<?php

namespace App\Listeners;

use App\Domain;
use App\Events\Domains\Saving;

class SetDomainFailedAt
{
    /**
     * Handle the event.
     *
     * @param Saving $event
     *
     * @return void
     */
    public function handle(Saving $event)
    {
        if ($event->domain->isDirty('reach_status')) {
            if ($this->isFailed($event->domain->reach_status) && $event->domain->failed_at === null) {
                $event->domain->failed_at = now()->toDateTimeString();
            }

            if (!$this->isFailed($event->domain->reach_status) && $event->domain->failed_at !== null) {
                $event->domain->failed_at = null;
            }
        }
    }

    private function isFailed($status)
    {
        return $status === Domain::BANNED || $status === Domain::MISSED;
    }
}
