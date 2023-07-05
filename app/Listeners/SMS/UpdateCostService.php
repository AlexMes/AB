<?php

namespace App\Listeners\SMS;

use App\Events\Sms\SmsMessageCreated;

class UpdateCostService
{

    /**
     * Handle the event.
     *
     * @param \App\Events\Sms\SmsMessageCreated $event
     *
     * @return void
     */
    public function handle(SmsMessageCreated $event)
    {
        if (optional($event->message->campaign->branch)->isSmsServiceValid()) {
            $service = $event->message->campaign->branch->initializeSmsService();
            $event->message->update([
                'cost' => round($event->message->raw_response['success_request']['cost']
                    ?? $service->getCost($event->message->getVendorId()), 2),
                'service' => get_class($service),
            ]);
        }
    }
}
