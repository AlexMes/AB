<?php

namespace App\Listeners\Google;

use App\Events\GoogleSheetCreated;

class DispatchSheetConfiguration
{
    /**
     * Handle the event.
     *
     * @param \App\Events\GoogleSheetCreated $event
     *
     * @return void
     */
    public function handle(GoogleSheetCreated $event)
    {
        $event->sheet->configure();
    }
}
