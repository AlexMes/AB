<?php

namespace App\Listeners;

use App\AdsBoard;
use App\Events\Lead\Created;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class PostbackTridentLead implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if ($event->lead->offer->hasLeadPostback()) {
            $addr     = $event->lead->offer->pb_lead.data_get($event->lead->requestData, 'app_external_id');
            $response = Http::get($addr);

            AdsBoard::report('Lead PB to '.$addr.'. Status: '.$response->status().' Body: '.$response->body());
        }
    }
}
