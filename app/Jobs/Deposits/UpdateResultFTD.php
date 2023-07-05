<?php

namespace App\Jobs\Deposits;

use App\Events\Deposits\Saved;

class UpdateResultFTD
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
        $result = $event->deposit->getCorrespondingResult();

        if ($result) {
            $result->refreshFtd();
        }
    }
}
