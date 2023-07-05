<?php

namespace App\Facebook\Listeners;

use App\Events\AdDisapproved;

class MarkAccountAdDisabled
{
    /**
     * Handle the event.
     *
     * @param AdDisapproved $event
     *
     * @return void
     */
    public function handle(AdDisapproved $event)
    {
        if ($event->ad->reject_reason === 'ADVERTISING_DISABLED_FOR_PAGE' && $event->ad->account->ad_disabled_at !== null) {
            $event->ad->account->update(['ad_disabled_at' => now()]);
        }
    }
}
