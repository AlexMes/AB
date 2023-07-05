<?php

namespace App\Listeners\SMS;

use App\Events\Sms\SmsMessageCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendSms implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * @var int
     */
    protected $delayBetweenTries = 20;

    /**
     * @var int
     */
    public $tries = 5;

    /**
     * Handle the event.
     *
     * @param SmsMessageCreated $event
     *
     * @throws Throwable
     *
     * @return void
     */
    public function handle(SmsMessageCreated $event)
    {
        if (optional($event->message->campaign->branch)->isSmsServiceValid()) {
            try {
                $response = $event->message->campaign->branch->initializeSmsService()
                    ->sendOne($event->message->phone, $event->message->message);
            } catch (Throwable $exception) {
                if ($this->attempts() < $this->tries) {
                    $this->release($this->delayBetweenTries);

                    return;
                }

                throw $exception;
            }

            $event->message->update([
                'raw_response' => $response,
            ]);
        }
    }
}
