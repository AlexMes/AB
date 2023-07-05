<?php

namespace App\Listeners;

use App\AdsBoard;
use App\Events\Deposits\Created;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class PostbackTridentDeposit implements ShouldQueue
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
        if ($event->deposit->lead->offer->hasDepositPostback()) {
            $addr     = $event->deposit->lead->offer->pb_sale.data_get($event->deposit->lead->requestData, 'app_external_id');
            $response = Http::get($addr);

            AdsBoard::report('Deposit PB to '.$addr.'. Status: '.$response->status().' Body: '.$response->body());
        }
    }
}
