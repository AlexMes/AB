<?php

namespace App\Listeners\Google;

use App\Events\GoogleSheetCreated;

class DispatchHeadersSetting
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
        $event->sheet->setupHeaders();
    }
}
