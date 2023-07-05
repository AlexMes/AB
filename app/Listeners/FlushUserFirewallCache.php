<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;

class FlushUserFirewallCache
{
    /**
     * Handle the event.
     *
     * @param \App\Events\FirewallRuleCreated $event
     *
     * @return void
     */
    public function handle($event)
    {
        Cache::forget(sprintf(
            'whitelist-%d',
            $event->rule->user_id,
        ));
    }
}
