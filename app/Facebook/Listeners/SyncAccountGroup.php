<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Profiles\Updating;

class SyncAccountGroup
{
    /**
     * sync profile group with account
     *
     * @param Updating $event
     */
    public function handle(Updating $event)
    {
        if ($event->profile->isDirty('group_id')) {
            $event->profile->syncGroup($event->profile->group_id, $event->profile->getRawOriginal('group_id'));
        }
    }
}
