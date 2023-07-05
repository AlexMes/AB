<?php

namespace App\Deluge\Listeners;

use App\Deluge\Events\Accounts\Saved;

class LogModerationStatus
{
    /**
     * Handle the event.
     *
     * @param Saved $event
     *
     * @return void
     */
    public function handle(Saved $event)
    {
        if ($event->account->isDirty('moderation_status')) {
            $event->account->moderationLogs()->create([
                'original'  => $event->account->getOriginal('moderation_status'),
                'changed'   => $event->account->moderation_status,
            ]);
        }
    }
}
