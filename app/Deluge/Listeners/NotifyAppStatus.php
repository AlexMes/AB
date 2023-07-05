<?php

namespace App\Deluge\Listeners;

use App\Deluge\Events\Apps\Saved;
use App\Notifications\ManualApps\StatusChanged;
use Illuminate\Support\Facades\Notification;

class NotifyAppStatus
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
        if ($event->app->isDirty('status') && !empty($event->app->chat_id)) {
            Notification::send(explode(',', $event->app->chat_id), new StatusChanged($event->app));
        }
    }
}
