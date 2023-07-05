<?php

namespace App\Listeners;

use App\AdsBoard;
use Illuminate\Auth\Events\Failed;

class ReportFailedLoginAttempt
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Failed $event)
    {
        AdsBoard::report(
            '*Failed login attempt* '.PHP_EOL.PHP_EOL.
            '*IP:* '.request()->getClientIp().PHP_EOL.
            '*Guard:* '.$event->guard.PHP_EOL.
            '*Email:* '.$event->credentials['email']
        );
    }
}
