<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Ads\Saved;

class ResolvePageId
{
    /**
     * Resolve and save page id
     *
     * @param \App\Facebook\Events\Ads\Saved $event
     */
    public function handle(Saved $event)
    {
        if ($event->ad->hasPostId()) {
            $event->ad->page_id = $event->ad->getPageId();
            $event->ad->saveQuietly();
        }
    }
}
