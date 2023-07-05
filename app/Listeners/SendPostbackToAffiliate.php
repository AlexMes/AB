<?php

namespace App\Listeners;

use App\Events\AssignmentSaved;
use App\Jobs\AffiliatePostback;
use Illuminate\Support\Facades\Log;

class SendPostbackToAffiliate
{
    /**
     * Handle the event.
     *
     * @param AssignmentSaved $event
     *
     * @return void
     */
    public function handle(AssignmentSaved $event)
    {
        if (
            $event->assignment->wasChanged('status')
            && $event->assignment->lead->hasAffiliate()
            && $event->assignment->lead->affiliate->postback !== null
        ) {
            Log::info('sending postback to affiliate');
            $status = in_array($event->assignment->status, ['Депозит', 'FTD','Ftd','Deposit','dep','Dep','Depozit']) ? 'В работе' : $event->assignment->status;

            AffiliatePostback::dispatch($event->assignment, $status);
        }
    }
}
