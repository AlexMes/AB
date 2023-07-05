<?php

namespace App\Listeners;

use App\Events\Tenants\Creating;

class GenerateTenantApiToken
{
    /**
     * Handle the event.
     *
     * @param Creating $event
     *
     * @return void
     */
    public function handle(Creating $event)
    {
        $event->tenant->generateApiToken();
    }
}
