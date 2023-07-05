<?php

namespace App\Listeners\Deposits;

use App\Events\Deposits\Updated;
use App\Result;

class CheckResult
{
    /**
     * Handle the event.
     *
     * @param Updated $event
     *
     * @return void
     */
    public function handle(Updated $event)
    {
        if ($event->deposit->isDirty(['office_id', 'offer_id', 'lead_return_date'])) {
            $oldResult = Result::query()
                ->where('date', $event->deposit->getOriginal('lead_return_date'))
                ->where('offer_id', $event->deposit->getOriginal('offer_id'))
                ->where('office_id', $event->deposit->getOriginal('office_id'))
                ->first();
            if ($oldResult !== null) {
                $oldResult->refreshFtd();
            }

            $event->deposit->updateCorrespondingResult();
        }
    }
}
