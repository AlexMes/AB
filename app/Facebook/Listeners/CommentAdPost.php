<?php

namespace App\Facebook\Listeners;

use App\Facebook\CommentTemplate;
use App\Facebook\Events\Ads\Created;
use App\Facebook\Events\Ads\Updated;
use App\Facebook\Jobs\SendComment;

class CommentAdPost
{
    /**
     * Handle event
     *
     * @param \App\Facebook\Events\Ads\Created $event
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle($event)
    {
        if ($event instanceof Created) {
            if ($event->ad->status === 'ACTIVE') {
                $this->comment($event->ad);
            }
        }

        if ($event instanceof Updated) {
            if (
                $event->ad->getRawOriginal()['effective_status'] === 'PENDING_REVIEW'
                && $event->ad->getDirty()['effective_status'] !== 'DISAPPROVED'
            ) {
                $this->comment($event->ad);
            };
        }
    }

    protected function comment($ad)
    {
        SendComment::dispatch(
            $ad,
            CommentTemplate::inRandomOrder()->first()
        );
    }
}
