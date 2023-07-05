<?php

namespace App\Listeners;

use App\CRM\Status;
use App\Events\Offices\Created;

class AttachStatusesToOffice
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
        $event->office->statuses()->createMany(
            Status::select('name')
                ->get()
                ->map(fn ($status) => ['status' => $status->name])
        );
    }
}
