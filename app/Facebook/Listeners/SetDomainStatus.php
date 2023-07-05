<?php

namespace App\Facebook\Listeners;

use App\Domain;
use FacebookAds\Object\Values\AdEffectiveStatusValues;

class SetDomainStatus
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $new = $event->ad->getDirty()['effective_status'] ?? null;

        if ($new && $event->ad->creative_url) {
            $creativeUrl = rtrim($event->ad->creative_url, '/');
            $domain      = Domain::query()
                ->where('url', 'like', "{$creativeUrl}%")
                ->first();

            if (!empty($domain)) {
                if ($new == AdEffectiveStatusValues::ACTIVE) {
                    $domain->update(['reach_status' => Domain::PASSED]);
                }
                if ($new == AdEffectiveStatusValues::DISAPPROVED) {
                    $domain->update(['reach_status' => Domain::MISSED]);
                }
            }
        }
    }
}
