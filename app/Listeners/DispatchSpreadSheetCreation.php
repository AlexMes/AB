<?php

namespace App\Listeners;

use App\Jobs\CreateManagerSpreadSheet;

class DispatchSpreadSheetCreation
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        CreateManagerSpreadSheet::dispatch($event->manager);
    }
}
