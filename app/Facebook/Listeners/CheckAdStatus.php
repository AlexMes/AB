<?php

namespace App\Facebook\Listeners;

use App\Events\AdDisapproved;
use App\Facebook\Events\Ads\Updated;

class CheckAdStatus
{
    public function handle(Updated $event)
    {
        if ($event->ad->isDirty(['reject_reason']) && $event->ad->reject_reason !== null) {
            AdDisapproved::dispatch($event->ad);
        }
    }
}
