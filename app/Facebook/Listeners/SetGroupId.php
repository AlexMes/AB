<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Accounts\Creating;
use App\Facebook\Events\Accounts\Updating;
use App\Facebook\Profile;

class SetGroupId
{
    /**
     * sync account group with profile group if needed
     *
     * @param Updating|Creating $event
     *
     * @return void
     */
    public function handle($event)
    {
        if (is_null($event->account->group_id)) {
            $event->account->group_id = optional(Profile::find($event->account->profile_id))->group_id;
        }
    }
}
