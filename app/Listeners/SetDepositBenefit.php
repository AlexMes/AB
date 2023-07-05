<?php

namespace App\Listeners;

use App\Events\Deposits\Created;

class SetDepositBenefit
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Deposits\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        $event->deposit->updateBenefit();
    }
}
