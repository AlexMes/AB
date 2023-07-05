<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Accounts\Updating;

class SetAccountBannedAt
{
    /**
     * Determine when we should change account banned state
     *
     * @param Updating $event
     *
     * @return void
     */
    public function handle(Updating $event)
    {
        if (
            $event->account->isDirty('account_status') &&
            $this->isBanned($event->account->account_status) &&
            $event->account->banned_at === null
        ) {
            $event->account->banned_at = now()->toDateTimeString();
        }

        if (
            $event->account->isDirty('account_status') &&
            !$this->isBanned($event->account->account_status) &&
            $event->account->banned_at !== null
        ) {
            $event->account->banned_at = null;
        }
    }


    /**
     * Determines banned state
     *
     * @param $value
     *
     * @return bool
     */
    protected function isBanned($value)
    {
        return in_array((int) $value, [2, 101, 202]);
    }
}
