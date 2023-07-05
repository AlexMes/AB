<?php

namespace App\Listeners;

use App\Events\LeadAssigned;

class IncrementOfficePaymentAssigned
{
    /**
     * Handle the event.
     *
     * @param LeadAssigned $event
     *
     * @return void
     */
    public function handle(LeadAssigned $event)
    {
        $officePayment = $event->assignment->route->order->office
            ->payments()
            ->incomplete()
            ->orderBy('created_at')
            ->first();

        if ($officePayment !== null) {
            $officePayment->increment('assigned');
        }
    }
}
