<?php

namespace App\Jobs\Deposits;

use App\Events\Deposits\Saved;

class FindDepositLead
{
    /**
     * Handle deposit event
     *
     * @param \App\Events\Deposits\Saved $event
     *
     * @return void
     */
    public function handle(Saved $event)
    {
        $lead = $event->deposit->getCorrespondingLead();

        if ($lead) {
            $event->deposit->update([
                'lead_id' => $lead->id,
            ]);
        }
    }
}
